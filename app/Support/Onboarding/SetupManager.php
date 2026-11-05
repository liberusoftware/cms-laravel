<?php

namespace App\Support\Onboarding;

use App\Models\PanelSetup;
use App\Models\Team;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Queries\CoreQueryService;

final class SetupManager
{
    public function authorize(?User $user, ?Team $team): void
    {
        abort_unless($user && ($team ? $user->ownsTeam($team) && $user->canAccessTenant($team) : $user->is_platform_admin), 403);
    }

    public function progress(?User $user, ?Team $team): ?PanelSetup
    {
        $this->authorize($user, $team);

        return PanelSetup::where('scope', $this->scope($team))->first();
    }

    /** @param array<string, mixed> $data */
    public function save(?User $user, ?Team $team, array $data, bool $complete = false): PanelSetup
    {
        $this->authorize($user, $team);
        if ($team && array_key_exists('domain', $data) && is_string($data['domain'])) {
            $data['domain'] = filled($data['domain']) ? strtolower(trim($data['domain'])) : null;
        }
        $required = $complete ? 'required' : 'nullable';
        $rules = $team ? [
            'name' => [$required, 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:253', 'regex:/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/i'],
            'default_locale' => [$required, Rule::in(['en', 'fr', 'de', 'es', 'it', 'pt', 'nl'])],
            'timezone' => [$required, 'timezone'],
        ] : [
            'site_name' => [$required, 'string', 'max:255'],
            'site_email' => [$required, 'email', 'max:255'],
            'site_default_language' => [$required, Rule::in(['en', 'fr', 'de', 'es', 'it', 'pt', 'nl'])],
            'site_currency' => [$required, Rule::in(['USD', 'GBP', 'EUR', 'CAD', 'AUD', 'NZD', 'JPY', 'CHF'])],
        ];
        $validated = Validator::make(['data' => $data], collect($rules)->mapWithKeys(fn ($rule, $key) => ['data.'.$key => $rule])->all())->validate()['data'];
        if (! is_array($validated)) {
            throw new \LogicException('Validated setup data must be an array.');
        }
        $values = [];
        foreach ($validated as $key => $value) {
            if (! is_string($key) || (! is_string($value) && $value !== null)) {
                throw new \LogicException('Validated setup fields must be strings or null.');
            }
            $values[$key] = $value;
        }

        try {
            return DB::transaction(function () use ($team, $values, $complete): PanelSetup {
                // Serialize submissions per team (or platform) before allocating a site.
                PanelSetup::query()->firstOrCreate(['scope' => $this->scope($team)], ['team_id' => $team?->id, 'data' => []]);
                $setup = PanelSetup::where('scope', $this->scope($team))->lockForUpdate()->firstOrFail();
                $setup->data = $values;
                if ($complete) {
                    if ($team) {
                        $this->saveSite($setup, $team, $values);
                    } else {
                        $settings = app(GeneralSettings::class);
                        foreach ($values as $key => $value) {
                            $settings->$key = $value;
                        }
                        $settings->save();
                    }
                    $setup->completed_at = now();
                }
                $setup->save();

                return $setup;
            });
        } catch (UniqueConstraintViolationException $exception) {
            if (str_contains($exception->getMessage(), 'domain')) {
                throw ValidationException::withMessages(['data.domain' => __('This domain is unavailable. Choose another domain or leave it blank for now.')]);
            }
            throw $exception;
        }
    }

    /** @param array<string, string|null> $values */
    private function saveSite(PanelSetup $setup, Team $team, array $values): void
    {
        $context = app(TenantContextInterface::class);
        $previous = $context->tenantId();
        $context->setTenantId($team->id);
        try {
            $queries = app(CoreQueryService::class);
            $actions = app(CoreMutationService::class);
            $key = 'website-team-'.$team->id;
            $site = $queries->site($key);
            abort_if($site && $site->getAttribute('team_id') != $team->id, 403);
            if ($site) {
                $site = $actions->updateSite($site, $values);
            } else {
                $site = $actions->createSite([...$values, 'key' => $key, 'status' => 'draft']);
            }
            $siteId = $site->getKey();
            if (! is_int($siteId) || $siteId < 1) {
                throw new \LogicException('A CMS site must have an integer ID.');
            }
            $setup->site_id = $siteId;
        } finally {
            $context->setTenantId($previous);
        }
    }

    private function scope(?Team $team): string
    {
        return $team ? 'team:'.$team->id : 'platform';
    }
}

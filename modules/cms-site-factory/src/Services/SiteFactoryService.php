<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Services;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\SiteFactory\Models\SiteDomain;
use Liberu\Cms\SiteFactory\Models\SiteFactoryOperation;
use Liberu\Cms\SiteFactory\Models\SiteTemplate;
use Throwable;

/** Authoritative Site Factory application boundary. */
final class SiteFactoryService
{
    /** @return Collection<int, SiteTemplate> */
    public function templates(): Collection
    {
        return SiteTemplate::query()->orderBy('key')->get();
    }

    /**
     * @param  array<string|int, mixed>  $configuration
     * @param  array<string|int, mixed>  $initialContent
     */
    public function template(string $key, string $name, array $configuration = [], array $initialContent = [], ?int $teamId = null): SiteTemplate
    {
        $key = Str::slug($key);
        if ($key === '' || trim($name) === '') {
            throw ValidationException::withMessages(['name' => 'Template key and name are required.']);
        }

        return SiteTemplate::query()->updateOrCreate(['key' => $key], ['name' => trim($name), 'configuration' => $configuration, 'initial_content' => $initialContent, 'active' => true, 'team_id' => $teamId]);
    }

    /**
     * @param  array<string|int, mixed>  $configuration
     * @param  array<string|int, mixed>  $initialContent
     */
    public function updateTemplate(SiteTemplate $template, string $name, array $configuration, array $initialContent): SiteTemplate
    {
        if (trim($name) === '') {
            throw ValidationException::withMessages(['name' => 'Template name is required.']);
        }
        $template->fill(['name' => trim($name), 'configuration' => $configuration, 'initial_content' => $initialContent])->save();

        return $template->refresh() ?? $template;
    }

    public function activateTemplate(SiteTemplate $template): SiteTemplate
    {
        $template->forceFill(['active' => true])->save();

        return $template->refresh() ?? $template;
    }

    public function deactivateTemplate(SiteTemplate $template): SiteTemplate
    {
        $template->forceFill(['active' => false])->save();

        return $template->refresh() ?? $template;
    }

    public function provision(string $key, string $name, ?string $templateKey = null, ?string $domain = null, ?int $teamId = null): Site
    {
        $key = Str::slug($key);
        if ($key === '' || trim($name) === '') {
            throw ValidationException::withMessages(['name' => 'Site key and name are required.']);
        }

        return $this->execute('provision', null, ['key' => $key, 'template' => $templateKey], function (SiteFactoryOperation $operation) use ($key, $name, $templateKey, $domain, $teamId): Site {
            return DB::transaction(function () use ($operation, $key, $name, $templateKey, $domain, $teamId): Site {
                if (Site::query()->where('key', $key)->exists()) {
                    throw ValidationException::withMessages(['key' => 'A site with this key already exists.']);
                }
                $template = null;
                if ($templateKey !== null) {
                    $template = SiteTemplate::query()->where('key', Str::slug($templateKey))->where('active', true)->firstOrFail();
                }
                $settings = ['factory' => ['configuration' => $template?->getAttribute('configuration') ?? [], 'initial_content' => $template?->getAttribute('initial_content') ?? []]];
                $site = Site::query()->create(['key' => $key, 'name' => trim($name), 'domain' => $domain, 'status' => 'active', 'settings' => $settings, 'team_id' => $teamId]);
                $operation->forceFill(['site_id' => $site->getKey(), 'team_id' => $site->getAttribute('team_id')])->save();
                if ($domain !== null && trim($domain) !== '') {
                    $this->addDomain($site, $domain);
                }

                return $site;
            });
        });
    }

    public function addDomain(Site $site, string $domain): SiteDomain
    {
        $domain = $this->normalizeDomain($domain);
        if (SiteDomain::query()->where('domain', $domain)->exists()) {
            throw ValidationException::withMessages(['domain' => 'This domain is already registered.']);
        }

        return SiteDomain::query()->create(['site_id' => $site->getKey(), 'domain' => $domain, 'verification_token' => hash('sha256', Str::random(64)), 'team_id' => $site->getAttribute('team_id')]);
    }

    public function verifyDomain(SiteDomain $domain, string $token): SiteDomain
    {
        if ($domain->getAttribute('verified_at') !== null) {
            return $domain->fresh() ?? $domain;
        }
        $verificationToken = $domain->getAttribute('verification_token');
        if (trim($token) === '' || ! is_string($verificationToken) || ! hash_equals($verificationToken, $token)) {
            throw ValidationException::withMessages(['token' => 'Domain verification failed.']);
        }
        $domain->forceFill(['verified_at' => now()])->save();

        return $domain->fresh() ?? $domain;
    }

    public function clone(Site $source, string $key, string $name): Site
    {
        return $this->execute('clone', $source, ['key' => Str::slug($key)], function (SiteFactoryOperation $operation) use ($source, $key, $name): Site {
            $teamId = $source->getAttribute('team_id');
            $clone = $this->provision($key, $name, null, null, is_int($teamId) ? $teamId : null);
            $clone->forceFill(['settings' => $source->getAttribute('settings')])->save();
            $operation->forceFill(['site_id' => $clone->getKey(), 'team_id' => $clone->getAttribute('team_id')])->save();

            return $clone->refresh() ?? $clone;
        });
    }

    public function suspend(Site $site): Site
    {
        return $this->transition($site, 'suspended');
    }

    public function archive(Site $site): Site
    {
        return $this->transition($site, 'archived');
    }

    public function transition(Site $site, string $status): Site
    {
        if (! in_array($status, ['active', 'suspended', 'archived'], true)) {
            throw ValidationException::withMessages(['status' => 'Unsupported site status.']);
        }
        if ($site->getAttribute('status') === 'archived' && $status !== 'archived') {
            throw ValidationException::withMessages(['status' => 'Archived sites cannot be reactivated.']);
        }
        if ($site->getAttribute('status') === $status) {
            return $site->refresh() ?? $site;
        }

        return $this->execute('transition', $site, ['from' => $site->getAttribute('status'), 'to' => $status], function () use ($site, $status): Site {
            return DB::transaction(function () use ($site, $status): Site {
                $site->forceFill(['status' => $status])->save();

                return $site->refresh() ?? $site;
            });
        });
    }

    public function teardown(Site $site, bool $confirm = false): void
    {
        if (! $confirm) {
            throw ValidationException::withMessages(['confirm' => 'Teardown requires explicit confirmation.']);
        }
        $this->execute('teardown', $site, [], function () use ($site): null {
            DB::transaction(function () use ($site): void {
                $site->delete();
            });

            return null;
        });
    }

    /** @return Collection<int, SiteFactoryOperation> */
    public function operations(?Site $site = null): Collection
    {
        $query = SiteFactoryOperation::query();
        if ($site !== null) {
            $query->where('site_id', $site->getKey());
        }

        return $query->latest()->get();
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));
        if (str_contains($domain, '/') || filter_var('https://'.$domain, FILTER_VALIDATE_URL) === false || ! preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $domain)) {
            throw ValidationException::withMessages(['domain' => 'Invalid domain.']);
        }

        return $domain;
    }

    /**
     * @template T
     *
     * @param  array<string, mixed>  $payload
     * @param  Closure(SiteFactoryOperation): T  $callback
     * @return T
     */
    private function execute(string $operation, ?Site $site, array $payload, Closure $callback): mixed
    {
        $record = SiteFactoryOperation::query()->create(['site_id' => $site?->getKey(), 'team_id' => $site?->getAttribute('team_id'), 'operation' => $operation, 'status' => 'pending', 'payload' => $payload]);
        try {
            $result = $callback($record);
            $record->forceFill(['status' => 'completed', 'completed_at' => now()])->save();

            return $result;
        } catch (Throwable $exception) {
            $record->forceFill(['status' => 'failed', 'error' => $exception->getMessage()])->save();
            throw $exception;
        }
    }
}

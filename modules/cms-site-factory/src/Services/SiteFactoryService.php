<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\SiteFactory\Models\SiteDomain;
use Liberu\Cms\SiteFactory\Models\SiteTemplate;

final class SiteFactoryService
{
    public function template(string $key, string $name, array $configuration = [], array $initialContent = []): SiteTemplate
    {
        return SiteTemplate::query()->updateOrCreate(['key' => Str::slug($key)], ['name' => $name, 'configuration' => $configuration, 'initial_content' => $initialContent, 'active' => true]);
    }

    public function provision(string $key, string $name, ?string $templateKey = null, ?string $domain = null, ?int $teamId = null): Site
    {
        if (Site::query()->where('key', $key)->exists()) {
            throw ValidationException::withMessages(['key' => 'A site with this key already exists.']);
        }
        $template = $templateKey ? SiteTemplate::query()->where('key', $templateKey)->where('active', true)->firstOrFail() : null;
        $site = Site::query()->create(['key' => Str::slug($key), 'name' => $name, 'domain' => $domain, 'status' => 'active', 'settings' => $template?->configuration ?? [], 'team_id' => $teamId]);
        if ($domain) {
            $this->addDomain($site, $domain);
        }

        return $site;
    }

    public function addDomain(Site $site, string $domain): SiteDomain
    {
        if (! filter_var('https://'.$domain, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['domain' => 'Invalid domain.']);
        }

return SiteDomain::query()->create(['site_id' => $site->getKey(), 'domain' => strtolower($domain), 'verification_token' => hash('sha256', Str::random(64))]);
    }

    public function verifyDomain(SiteDomain $domain, string $token): SiteDomain
    {
        if (! hash_equals($domain->verification_token, $token)) {
            throw ValidationException::withMessages(['token' => 'Domain verification failed.']);
        } $domain->forceFill(['verified_at' => now()])->save();

        return $domain->fresh();
    }

    public function clone(Site $source, string $key, string $name): Site
    {
        $clone = $this->provision($key, $name, null, null, $source->team_id);
        $clone->forceFill(['settings' => $source->settings])->save();

        return $clone;
    }

    public function suspend(Site $site): Site
    {
        return $this->transition($site, 'suspended');
    }

    public function archive(Site $site): Site
    {
        return $this->transition($site, 'archived');
    }

    public function teardown(Site $site, bool $confirm = false): void
    {
        if (! $confirm) {
            throw ValidationException::withMessages(['confirm' => 'Teardown requires explicit confirmation.']);
        } $site->delete();
    }

    private function transition(Site $site, string $status): Site
    {
        if ($site->status === 'archived' && $status !== 'archived') {
            throw ValidationException::withMessages(['status' => 'Archived sites cannot be reactivated.']);
        } $site->forceFill(['status' => $status])->save();

        return $site->fresh();
    }
}

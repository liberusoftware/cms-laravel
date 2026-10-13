<?php

namespace Liberu\Cms\Embeds\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\Embeds\Models\Embed;
use Liberu\Cms\Embeds\Models\Provider;

class EmbedsService
{
    public function provider(array $a, ?int $teamId = null): Provider
    {
        if (! preg_match('/^[a-z0-9][a-z0-9._-]*$/', $a['key'] ?? '')) {
            throw ValidationException::withMessages(['key' => 'Invalid provider key.']);
        }

        return Provider::updateOrCreate(['team_id' => $teamId, 'key' => $a['key']], ['name' => $a['name'], 'domain_pattern' => $a['domain_pattern'] ?? null, 'privacy_domain' => $a['privacy_domain'] ?? null, 'status' => $a['status'] ?? 'active', 'config' => $a['config'] ?? []]);
    }

    public function embed(array $a, ?int $teamId = null): Embed
    {
        $provider = Provider::whereKey($a['provider_id'])->firstOrFail();
        $this->url($a['url'], $provider);
        if (! empty($a['fallback_url'])) {
            $this->url($a['fallback_url']);
        } $mode = $a['privacy_mode'] ?? 'public';
        if (! in_array($mode, ['public', 'private', 'consent'], true)) {
            throw ValidationException::withMessages(['privacy_mode' => 'Invalid privacy mode.']);
        }

        return Embed::updateOrCreate(['team_id' => $teamId, 'provider_id' => $provider->id, 'external_key' => $a['external_key']], array_merge($a, ['team_id' => $teamId, 'consent_required' => $mode === 'consent', 'status' => $a['status'] ?? 'draft']));
    }

    public function publish(Embed $e): Embed
    {
        if ($e->provider?->status !== 'active') {
            throw ValidationException::withMessages(['provider' => 'Provider is inactive.']);
        } $e->update(['status' => 'published']);

        return $e->refresh();
    }

    public function render(Embed $e, bool $consented = false): array
    {
        if ($e->status !== 'published' || ($e->privacy_mode === 'consent' && ! $consented)) {
            return ['status' => 'fallback', 'url' => $e->fallback_url, 'title' => $e->title];
        }

        return ['status' => 'published', 'url' => $e->url, 'title' => $e->title, 'provider' => $e->provider?->key, 'responsive' => $e->responsive, 'aspect_ratio' => $e->aspect_ratio];
    }

    private function url(string $url, ?Provider $p = null): string
    {
        $x = parse_url($url);
        if (! $x || ! in_array($x['scheme'] ?? '', ['http', 'https'], true) || empty($x['host']) || isset($x['user'],$x['pass'])) {
            throw ValidationException::withMessages(['url' => 'Invalid embed URL.']);
        } if ($p?->domain_pattern && ($x['host'] !== $p->domain_pattern && (! str_starts_with($p->domain_pattern, '*.') || ! str_ends_with($x['host'], substr($p->domain_pattern, 1))))) {
            throw ValidationException::withMessages(['url' => 'URL is not allowed for this provider.']);
        }

        return $url;
    }
}

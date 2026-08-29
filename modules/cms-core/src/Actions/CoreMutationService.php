<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Core\Models\Channel;
use Liberu\Cms\Core\Models\ContentAlias;
use Liberu\Cms\Core\Models\ContentIdentity;
use Liberu\Cms\Core\Models\Setting;
use Liberu\Cms\Core\Models\Site;

/**
 * Authoritative mutation boundary for CMS Core.
 *
 * Presentation adapters should call this service instead of constructing Core
 * models directly. Database constraints remain the final duplicate/concurrency
 * guard; these checks provide stable validation errors before persistence.
 */
final class CoreMutationService
{
    /** @param array<string, mixed> $attributes */
    public function createSite(array $attributes): Site
    {
        $this->requireString($attributes, 'key', 255);
        $this->requireString($attributes, 'name', 255);

        $attributes = array_intersect_key($attributes, array_flip([
            'key', 'name', 'domain', 'default_locale', 'timezone', 'status', 'settings',
        ]));

        return DB::transaction(fn (): Site => Site::create([
            ...$attributes,
            'status' => $attributes['status'] ?? 'active',
            'default_locale' => $attributes['default_locale'] ?? 'en',
            'timezone' => $attributes['timezone'] ?? 'UTC',
        ]));
    }

    /** @param array<string, mixed> $attributes */
    public function createChannel(Site|int|string $site, array $attributes): Channel
    {
        $site = $this->site($site);
        $this->requireString($attributes, 'key', 255);
        $this->requireString($attributes, 'name', 255);

        $attributes = array_intersect_key($attributes, array_flip(['key', 'name', 'type', 'settings']));

        return DB::transaction(fn (): Channel => $site->channels()->create([
            ...$attributes,
            'type' => $attributes['type'] ?? 'web',
        ]));
    }

    /** @param array<string, mixed> $attributes */
    public function updateSite(Site $site, array $attributes): Site
    {
        foreach (['key', 'name'] as $required) {
            if (array_key_exists($required, $attributes)) {
                $this->requireString($attributes, $required, 255);
            }
        }

        return DB::transaction(function () use ($site, $attributes): Site {
            $site->fill(array_intersect_key($attributes, array_flip([
                'key', 'name', 'domain', 'default_locale', 'timezone', 'status', 'settings',
            ])));
            $site->save();

            return $site;
        });
    }

    /** @param array<string, mixed> $attributes */
    public function updateChannel(Channel $channel, array $attributes): Channel
    {
        foreach (['key', 'name'] as $required) {
            if (array_key_exists($required, $attributes)) {
                $this->requireString($attributes, $required, 255);
            }
        }

        return DB::transaction(function () use ($channel, $attributes): Channel {
            $channel->fill(array_intersect_key($attributes, array_flip(['key', 'name', 'type', 'settings'])));
            $channel->save();

            return $channel;
        });
    }

    public function deleteSite(Site $site): void
    {
        DB::transaction(fn (): ?bool => $site->delete());
    }

    public function deleteChannel(Channel $channel): void
    {
        DB::transaction(fn (): ?bool => $channel->delete());
    }

    /** @param array<string, mixed> $attributes */
    public function createIdentity(Site|int|string $site, array $attributes): ContentIdentity
    {
        $site = $this->site($site);
        $this->requireString($attributes, 'content_type', 255);
        $this->requireString($attributes, 'content_id', 255);
        $this->validateChannel($site, $attributes['channel_id'] ?? null);

        $attributes = array_intersect_key($attributes, array_flip([
            'channel_id', 'content_type', 'content_id', 'canonical_path', 'status',
            'owner_type', 'owner_id', 'metadata',
        ]));

        return DB::transaction(fn (): ContentIdentity => $site->identities()->create($attributes));
    }

    /** @param array<string, mixed> $attributes */
    public function createAlias(Site|int|string $site, array $attributes): ContentAlias
    {
        $site = $this->site($site);
        $this->requireString($attributes, 'alias', 255);
        $this->requireString($attributes, 'target_type', 255);
        $this->requireString($attributes, 'target_id', 255);
        $this->validateChannel($site, $attributes['channel_id'] ?? null);
        $this->validateRedirectStatus($attributes['redirect_status'] ?? 301);

        $alias = '/'.ltrim(trim((string) $attributes['alias']), '/');

        return DB::transaction(fn (): ContentAlias => $site->aliases()->create([
            ...array_intersect_key($attributes, array_flip([
                'channel_id', 'alias', 'target_type', 'target_id', 'redirect_status',
            ])),
            'alias' => $alias,
            'redirect_status' => $attributes['redirect_status'] ?? 301,
        ]));
    }

    /** @param array<string, mixed> $value */
    public function putSetting(?Site $site, string $key, array $value, string $environment = 'production'): Setting
    {
        if (trim($key) === '' || mb_strlen($key) > 255) {
            throw ValidationException::withMessages(['key' => 'A setting key is required and must be 255 characters or fewer.']);
        }
        if (trim($environment) === '' || mb_strlen($environment) > 255) {
            throw ValidationException::withMessages(['environment' => 'An environment is required and must be 255 characters or fewer.']);
        }

        return DB::transaction(function () use ($site, $key, $value, $environment): Setting {
            $query = Setting::query()->where('key', $key)->where('environment', $environment);
            $site?->getKey() === null ? $query->whereNull('site_id') : $query->where('site_id', $site->getKey());

            $setting = $query->firstOrNew([
                'site_id' => $site?->getKey(),
                'key' => $key,
                'environment' => $environment,
            ]);
            $setting->fill(['value' => $value]);
            $setting->save();

            return $setting;
        });
    }

    private function site(Site|int|string $site): Site
    {
        if ($site instanceof Site) {
            return $site;
        }

        return is_numeric($site)
            ? Site::query()->findOrFail((int) $site)
            : Site::query()->where('key', $site)->firstOrFail();
    }

    private function validateChannel(Site $site, mixed $channelId): void
    {
        if ($channelId === null) {
            return;
        }

        if (! is_int($channelId) && (! is_string($channelId) || ! ctype_digit($channelId))) {
            throw ValidationException::withMessages(['channel_id' => 'The channel must belong to the selected site.']);
        }

        if (! $site->channels()->whereKey((int) $channelId)->exists()) {
            throw ValidationException::withMessages(['channel_id' => 'The channel must belong to the selected site.']);
        }
    }

    private function validateRedirectStatus(mixed $status): void
    {
        if (! in_array($status, [301, 302, 307, 308], true)) {
            throw ValidationException::withMessages(['redirect_status' => 'The redirect status must be 301, 302, 307, or 308.']);
        }
    }

    /** @param array<string, mixed> $attributes */
    private function requireString(array $attributes, string $key, int $maxLength): void
    {
        $value = $attributes[$key] ?? null;

        if (! is_string($value) || trim($value) === '' || mb_strlen($value) > $maxLength) {
            throw ValidationException::withMessages([$key => "The {$key} field is required and must be {$maxLength} characters or fewer."]);
        }
    }
}

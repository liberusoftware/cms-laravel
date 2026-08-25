<?php

declare(strict_types=1);

namespace Liberu\Cms\Multisite\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Multisite\Models\SiteAdmin;
use Liberu\Cms\Multisite\Models\SiteQuota;
use Liberu\Cms\Multisite\Models\SiteReference;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;

/** Authoritative network, governance, quota, and cross-site reference boundary. */
final readonly class MultisiteService
{
    public function __construct(private SiteFactoryService $factory) {}

    public function createSite(string $key, string $name, ?string $domain = null, ?int $teamId = null): Site
    {
        return $this->factory->provision($key, $name, domain: $domain, teamId: $teamId);
    }

    public function transition(Site $site, string $status): Site
    {
        if (! in_array($status, ['active', 'suspended', 'archived'], true)) {
            throw ValidationException::withMessages(['status' => 'The site status is invalid.']);
        }
        if ($site->status === 'archived' && $status !== 'archived') {
            throw ValidationException::withMessages(['status' => 'Archived sites cannot be reactivated.']);
        }

        $site->forceFill(['status' => $status])->save();

        return $site->refresh();
    }

    /** @param array<string, int> $limits */
    public function setQuota(Site $site, array $limits): SiteQuota
    {
        foreach ($limits as $key => $value) {
            if (! in_array($key, ['storage_bytes', 'content_items', 'bandwidth_bytes'], true) || $value < 0) {
                throw ValidationException::withMessages(['limits' => 'Quota keys or values are invalid.']);
            }
        }

        return SiteQuota::query()->updateOrCreate(
            ['site_id' => $site->getKey()],
            ['limits' => $limits, 'team_id' => $site->team_id],
        );
    }

    /** @param array<string, int> $delta */
    public function recordUsage(SiteQuota $quota, array $delta): SiteQuota
    {
        $usage = $quota->usage ?? [];
        $limits = $quota->limits ?? [];
        foreach ($delta as $key => $value) {
            $next = (int) ($usage[$key] ?? 0) + $value;
            if ($value < 0 || (isset($limits[$key]) && $next > (int) $limits[$key])) {
                throw ValidationException::withMessages(['usage' => 'The quota would be exceeded.']);
            }
            $usage[$key] = $next;
        }
        $quota->update(['usage' => $usage]);

        return $quota->refresh();
    }

    public function assignAdmin(Site $site, int $userId, string $role = 'editor'): SiteAdmin
    {
        if (! in_array($role, ['owner', 'admin', 'editor', 'viewer'], true)) {
            throw ValidationException::withMessages(['role' => 'The site administrator role is invalid.']);
        }

        return SiteAdmin::query()->updateOrCreate(
            ['site_id' => $site->getKey(), 'user_id' => $userId],
            ['role' => $role, 'active' => true, 'team_id' => $site->team_id],
        );
    }

    public function removeAdmin(SiteAdmin $admin): void
    {
        $admin->delete();
    }

    public function reference(Site $source, Site $target, string $contentType, string $contentId, string $mode = 'shared'): SiteReference
    {
        if ($source->is($target) || trim($contentType) === '' || trim($contentId) === '') {
            throw ValidationException::withMessages(['reference' => 'A reference needs distinct sites and a content identifier.']);
        }
        if (! in_array($mode, ['shared', 'isolated'], true)) {
            throw ValidationException::withMessages(['mode' => 'Reference mode must be shared or isolated.']);
        }

        return DB::transaction(fn (): SiteReference => SiteReference::create([
            'source_site_id' => $source->getKey(), 'target_site_id' => $target->getKey(),
            'content_type' => $contentType, 'content_id' => $contentId, 'mode' => $mode,
            'team_id' => $source->team_id,
        ]));
    }

    /** @param list<int> $siteIds */
    public function networkTransition(array $siteIds, string $status): int
    {
        if (! in_array($status, ['active', 'suspended', 'archived'], true)) {
            throw ValidationException::withMessages(['status' => 'The site status is invalid.']);
        }

        return DB::transaction(fn (): int => Site::query()->whereIn('id', $siteIds)->update(['status' => $status, 'updated_at' => now()]));
    }
}

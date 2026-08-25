<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\TranslationAssistant\Models\GlossaryEntry;
use Liberu\Cms\TranslationAssistant\Models\StyleRule;
use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;

final class TranslationAssistantQuery
{
    public function __construct(private readonly TenantContextInterface $tenant) {}

    private function tenantId(?int $teamId): int|string|null
    {
        return $teamId ?? $this->tenant->tenantId();
    }

    public function drafts(array $filters = [], int $perPage = 25, ?int $teamId = null): LengthAwarePaginator
    {
        return TranslationDraft::query()->where('team_id', $this->tenantId($teamId))
            ->when($filters['subject_type'] ?? null, fn ($q, string $v) => $q->where('subject_type', $v))
            ->when($filters['subject_id'] ?? null, fn ($q, string $v) => $q->where('subject_id', $v))
            ->when($filters['target_locale'] ?? null, fn ($q, string $v) => $q->where('target_locale', $v))
            ->when($filters['status'] ?? null, fn ($q, string $v) => $q->where('status', $v))
            ->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function draft(int|string $id, ?int $teamId = null): ?TranslationDraft
    {
        return TranslationDraft::query()->whereKey($id)->where('team_id', $this->tenantId($teamId))->first();
    }

    /** @return array<int, GlossaryEntry> */
    public function glossary(?string $sourceLocale = null, ?string $targetLocale = null, ?int $teamId = null): array
    {
        return GlossaryEntry::query()->where('team_id', $this->tenantId($teamId))->when($sourceLocale, fn ($q, string $v) => $q->where('source_locale', $v))->when($targetLocale, fn ($q, string $v) => $q->where('target_locale', $v))->orderBy('source_term')->get()->all();
    }

    /** @return array<int, StyleRule> */
    public function styleRules(?string $locale = null, ?int $teamId = null): array
    {
        return StyleRule::query()->where('team_id', $this->tenantId($teamId))->when($locale, fn ($q, string $v) => $q->where('locale', $v))->orderBy('name')->get()->all();
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\TranslationManagement\Events\SourceChangeTranslated;
use Liberu\Cms\TranslationManagement\Events\TranslationJobCreated;
use Liberu\Cms\TranslationManagement\Events\TranslationJobReconciled;
use Liberu\Cms\TranslationManagement\Events\TranslationReviewed;
use Liberu\Cms\TranslationManagement\Models\TranslationAssignment;
use Liberu\Cms\TranslationManagement\Models\TranslationGlossary;
use Liberu\Cms\TranslationManagement\Models\TranslationJob;
use Liberu\Cms\TranslationManagement\Models\TranslationMemory;
use Liberu\Cms\TranslationManagement\Models\TranslationSourceChange;
use Liberu\Cms\TranslationManagement\Models\TranslationVendor;
use Liberu\Cms\TranslationManagement\Support\TranslationVendorRegistry;

final class TranslationManagementService
{
    public function __construct(private readonly TranslationVendorRegistry $vendors) {}

    public function createJob(array $attributes): TranslationJob
    {
        $this->validateLocales($attributes['source_locale'] ?? null, $attributes['target_locale'] ?? null);
        $job = DB::transaction(fn (): TranslationJob => TranslationJob::create([
            ...$attributes,
            'public_id' => $attributes['public_id'] ?? (string) Str::uuid(),
            'status' => $attributes['status'] ?? 'draft',
            'currency' => $attributes['currency'] ?? config('translation-management.cost_currency', 'USD'),
        ]));
        event(new TranslationJobCreated($job));

        return $job;
    }

    public function addSourceChange(TranslationJob $job, array $attributes): TranslationSourceChange
    {
        $this->assertMutable($job);
        $text = (string) ($attributes['source_text'] ?? '');
        if ($text === '' || trim((string) ($attributes['field'] ?? '')) === '') {
            throw ValidationException::withMessages(['source_text' => 'Source text and field are required.']);
        }

        return DB::transaction(function () use ($job, $attributes, $text): TranslationSourceChange {
            $change = $job->sourceChanges()->firstOrCreate([
                'subject_type' => (string) ($attributes['subject_type'] ?? ''),
                'subject_id' => (string) ($attributes['subject_id'] ?? ''),
                'field' => (string) $attributes['field'],
                'source_hash' => hash('sha256', $text),
            ], [
                'source_text' => $text,
                'source_version' => $attributes['source_version'] ?? null,
                'team_id' => $attributes['team_id'] ?? $job->team_id,
            ]);
            $job->increment('total_units', $change->wasRecentlyCreated ? 1 : 0);

            return $change->refresh();
        });
    }

    public function queue(TranslationJob $job): TranslationJob
    {
        $this->assertMutable($job);
        $job->update(['status' => 'queued', 'queued_at' => now()]);

        return $job->refresh();
    }

    public function translate(TranslationSourceChange $change, string $vendorKey, array $context = []): TranslationSourceChange
    {
        $job = $change->job()->firstOrFail();
        $this->assertMutable($job);
        if ($change->translated_text !== null && $change->status === 'in_review') {
            return $change;
        }
        $result = $this->vendors->resolve($vendorKey)->translate($change->source_text, $job->source_locale, $job->target_locale, $context);
        $translated = DB::transaction(function () use ($change, $job, $result, $vendorKey): TranslationSourceChange {
            $change->update([
                'translated_text' => $result->text,
                'translated_hash' => hash('sha256', $result->text),
                'status' => 'in_review',
                'provider' => $result->provider ?: $vendorKey,
                'model' => $result->model,
                'cost' => $result->cost,
                'provenance' => $result->provenance,
                'translated_at' => now(),
            ]);
            $job->update(['status' => 'translating', 'vendor_key' => $vendorKey]);

            return $change->refresh();
        });
        event(new SourceChangeTranslated($translated));

        return $translated;
    }

    public function assign(TranslationSourceChange $change, string $assigneeType, int|string $assigneeId, string $role, ?string $dueAt = null): TranslationAssignment
    {
        if (! in_array($role, config('translation-management.assignment_roles', []), true)) {
            throw ValidationException::withMessages(['role' => 'Unsupported translation assignment role.']);
        }
        $job = $change->job()->firstOrFail();
        $this->assertMutable($job);

        return TranslationAssignment::query()->updateOrCreate([
            'job_id' => $job->getKey(), 'source_change_id' => $change->getKey(), 'assignee_type' => $assigneeType, 'assignee_id' => (string) $assigneeId, 'role' => $role,
        ], ['due_at' => $dueAt, 'status' => 'assigned', 'team_id' => $job->team_id]);
    }

    public function review(TranslationSourceChange $change, string $decision, ?string $notes = null): TranslationSourceChange
    {
        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['decision' => 'Review decision must be approved or rejected.']);
        }
        if ($change->translated_text === null) {
            throw ValidationException::withMessages(['translated_text' => 'Only translated source changes can be reviewed.']);
        }
        $change->update(['status' => $decision, 'review_notes' => $notes === null ? null : ['note' => $notes], 'reviewed_at' => now()]);
        if ($decision === 'approved') {
            $job = $change->job()->firstOrFail();
            $this->remember($job->source_locale, $job->target_locale, $change->source_text, $change->translated_text, ['source_change_id' => $change->getKey()], $job->team_id);
        }
        $reviewed = $change->refresh();
        event(new TranslationReviewed($reviewed, $decision));

        return $reviewed;
    }

    public function addVendor(array $attributes): TranslationVendor
    {
        if (blank($attributes['key'] ?? null) || blank($attributes['driver'] ?? null)) {
            throw ValidationException::withMessages(['key' => 'Vendor key and driver are required.']);
        }

        return TranslationVendor::query()->updateOrCreate(['key' => $attributes['key'], 'team_id' => $attributes['team_id'] ?? null], $attributes);
    }

    public function remember(string $sourceLocale, string $targetLocale, string $sourceText, string $translatedText, array $metadata = [], int|string|null $teamId = null): TranslationMemory
    {
        $this->validateLocales($sourceLocale, $targetLocale);

        return TranslationMemory::query()->updateOrCreate([
            'source_locale' => $sourceLocale, 'target_locale' => $targetLocale, 'source_hash' => hash('sha256', $sourceText), 'team_id' => $teamId,
        ], ['source_text' => $sourceText, 'translated_text' => $translatedText, 'status' => 'approved', 'metadata' => $metadata]);
    }

    public function glossary(array $attributes): TranslationGlossary
    {
        $this->validateLocales($attributes['source_locale'] ?? null, $attributes['target_locale'] ?? null);
        if (blank($attributes['source_term'] ?? null) || blank($attributes['preferred_term'] ?? null)) {
            throw ValidationException::withMessages(['source_term' => 'Source and preferred terms are required.']);
        }

        return TranslationGlossary::query()->updateOrCreate([
            'source_locale' => $attributes['source_locale'], 'target_locale' => $attributes['target_locale'], 'source_term' => $attributes['source_term'], 'team_id' => $attributes['team_id'] ?? null,
        ], ['preferred_term' => $attributes['preferred_term'], 'forbidden_terms' => $attributes['forbidden_terms'] ?? []]);
    }

    public function reconcile(TranslationJob $job): TranslationJob
    {
        $approved = $job->sourceChanges()->where('status', 'approved')->count();
        $total = $job->sourceChanges()->count();
        $status = $total > 0 && $approved === $total ? 'completed' : ($job->status === 'cancelled' ? 'cancelled' : 'in_review');
        $job->update(['completed_units' => $approved, 'actual_cost' => $job->sourceChanges()->sum('cost'), 'status' => $status, 'completed_at' => $status === 'completed' ? now() : null]);
        $reconciled = $job->refresh();
        event(new TranslationJobReconciled($reconciled));

        return $reconciled;
    }

    private function validateLocales(mixed $source, mixed $target): void
    {
        if (! is_string($source) || ! is_string($target) || trim($source) === '' || trim($target) === '' || $source === $target) {
            throw ValidationException::withMessages(['target_locale' => 'Source and target locales must be present and different.']);
        }
    }

    private function assertMutable(TranslationJob $job): void
    {
        if (in_array($job->status, ['completed', 'cancelled'], true)) {
            throw ValidationException::withMessages(['status' => 'This translation job is no longer mutable.']);
        }
    }
}

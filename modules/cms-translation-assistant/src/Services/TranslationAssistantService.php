<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\TranslationAssistant\Models\GlossaryEntry;
use Liberu\Cms\TranslationAssistant\Models\StyleRule;
use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;

final class TranslationAssistantService
{
    public function draft(string $subjectType, int|string $subjectId, string $sourceLocale, string $targetLocale, string $sourceText, string $translatedText, float $confidence, string $provider, string $model, array $provenance = [], ?int $teamId = null): TranslationDraft
    {
        foreach (compact('subjectType', 'sourceLocale', 'targetLocale', 'sourceText', 'translatedText', 'provider', 'model') as $field => $value) {
            if (trim((string) $value) === '') {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if ($sourceLocale === $targetLocale) {
            throw ValidationException::withMessages(['target_locale' => 'Source and target locales must differ.']);
        }
        if ($confidence < 0 || $confidence > 1) {
            throw ValidationException::withMessages(['confidence' => 'Confidence must be between 0 and 1.']);
        }
        $draft = TranslationDraft::query()->create(['subject_type' => $subjectType, 'subject_id' => (string) $subjectId, 'source_locale' => $sourceLocale, 'target_locale' => $targetLocale, 'source_text' => $sourceText, 'translated_text' => $translatedText, 'confidence' => $confidence, 'status' => 'draft', 'provider' => $provider, 'model' => $model, 'provenance' => $provenance, 'violations' => [], 'team_id' => $teamId]);

        return $this->check($draft);
    }

    public function check(TranslationDraft $draft): TranslationDraft
    {
        $violations = [];
        foreach (GlossaryEntry::query()->where('source_locale', $draft->source_locale)->where('target_locale', $draft->target_locale)->where('team_id', $draft->team_id)->get() as $entry) {
            if (str_contains(mb_strtolower($draft->source_text), mb_strtolower($entry->source_term)) && ! str_contains(mb_strtolower($draft->translated_text), mb_strtolower($entry->preferred_term))) {
                $violations[] = ['type' => 'glossary', 'message' => "Use '{$entry->preferred_term}' for '{$entry->source_term}'."];
            }
            foreach ($entry->forbidden_terms ?? [] as $forbidden) {
                if (str_contains(mb_strtolower($draft->translated_text), mb_strtolower((string) $forbidden))) {
                    $violations[] = ['type' => 'forbidden-term', 'message' => "Forbidden term: {$forbidden}."];
                }
            }
        }
        foreach (StyleRule::query()->where('locale', $draft->target_locale)->where('team_id', $draft->team_id)->get() as $rule) {
            if (@preg_match($rule->pattern, $draft->translated_text) === 1) {
                $violations[] = ['type' => 'style', 'severity' => $rule->severity, 'message' => $rule->message];
            }
        }
        $draft->update(['violations' => $violations]);

        return $draft->refresh();
    }

    public function review(TranslationDraft $draft, string $decision, string $reviewerType, int|string $reviewerId): TranslationDraft
    {
        if (! in_array($decision, ['approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['status' => 'Review decision must be approved or rejected.']);
        }
        if ($decision === 'approved' && $draft->violations !== []) {
            throw ValidationException::withMessages(['status' => 'A draft with glossary or style violations cannot be approved.']);
        }
        $draft->update(['status' => $decision, 'reviewer_type' => $reviewerType, 'reviewer_id' => (string) $reviewerId, 'reviewed_at' => now()]);

        return $draft->refresh();
    }

    public function updateDraft(TranslationDraft $draft, array $attributes): TranslationDraft
    {
        if (array_key_exists('translated_text', $attributes) && trim((string) $attributes['translated_text']) === '') {
            throw ValidationException::withMessages(['translated_text' => 'Translated text is required.']);
        }
        if (array_key_exists('confidence', $attributes) && ((float) $attributes['confidence'] < 0 || (float) $attributes['confidence'] > 1)) {
            throw ValidationException::withMessages(['confidence' => 'Confidence must be between 0 and 1.']);
        }
        $draft->update(array_intersect_key($attributes, array_flip(['translated_text', 'confidence', 'provenance'])));

        return $this->check($draft);
    }

    public function removeDraft(TranslationDraft $draft): void
    {
        $draft->delete();
    }

    public function addGlossary(string $sourceLocale, string $targetLocale, string $sourceTerm, string $preferredTerm, array $forbiddenTerms = [], ?int $teamId = null): GlossaryEntry
    {
        return GlossaryEntry::query()->updateOrCreate(['source_locale' => $sourceLocale, 'target_locale' => $targetLocale, 'source_term' => $sourceTerm, 'team_id' => $teamId], ['preferred_term' => $preferredTerm, 'forbidden_terms' => $forbiddenTerms, 'team_id' => $teamId]);
    }

    public function addStyleRule(string $locale, string $name, string $pattern, string $message, string $severity = 'warning', ?int $teamId = null): StyleRule
    {
        if (@preg_match($pattern, '') === false) {
            throw ValidationException::withMessages(['pattern' => 'Style rule must be a valid regular expression.']);
        }

return StyleRule::query()->create(compact('locale', 'name', 'pattern', 'message', 'severity', 'teamId') + ['team_id' => $teamId]);
    }
}

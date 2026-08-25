<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantApi\Http\Resources;

use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;

final class TranslationDraftResource
{
    /** @return array<string, mixed> */
    public static function make(TranslationDraft $draft): array
    {
        return ['id' => (string) $draft->getKey(), 'type' => 'cms-translation-draft', 'subject_type' => $draft->subject_type, 'subject_id' => $draft->subject_id, 'source_locale' => $draft->source_locale, 'target_locale' => $draft->target_locale, 'source_text' => $draft->source_text, 'translated_text' => $draft->translated_text, 'confidence' => (float) $draft->confidence, 'status' => $draft->status, 'violations' => $draft->violations ?? [], 'provider' => $draft->provider, 'model' => $draft->model, 'provenance' => $draft->provenance ?? [], 'reviewed_at' => $draft->reviewed_at?->toISOString()];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;
use Liberu\Cms\TranslationAssistant\Queries\TranslationAssistantQuery;
use Liberu\Cms\TranslationAssistant\Services\TranslationAssistantService;
use Liberu\Cms\TranslationAssistantApi\Http\Resources\TranslationDraftResource;
use Liberu\Cms\TranslationAssistantApi\Http\Resources\GlossaryEntryResource;
use Liberu\Cms\TranslationAssistantApi\Http\Resources\StyleRuleResource;

final class TranslationAssistantController
{
    public function index(Request $request, TranslationAssistantQuery $query): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['sometimes', 'string'], 'subject_id' => ['sometimes', 'string'], 'target_locale' => ['sometimes', 'string'], 'status' => ['sometimes', 'string']]);
        $drafts = $query->drafts($data, $request->integer('per_page', 25));
        return response()->json(['data' => array_map(TranslationDraftResource::make(...), $drafts->items()), 'meta' => ['current_page' => $drafts->currentPage(), 'last_page' => $drafts->lastPage(), 'per_page' => $drafts->perPage(), 'total' => $drafts->total()]]);
    }

    public function show(int|string $draft, TranslationAssistantQuery $query): JsonResponse { $model = $query->draft($draft); abort_unless($model, 404); return response()->json(['data' => TranslationDraftResource::make($model)]); }

    public function create(Request $request, TranslationAssistantService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string', 'max:150'], 'subject_id' => ['required', 'string', 'max:100'], 'source_locale' => ['required', 'string', 'max:20'], 'target_locale' => ['required', 'string', 'max:20'], 'source_text' => ['required', 'string'], 'translated_text' => ['required', 'string'], 'confidence' => ['required', 'numeric', 'between:0,1'], 'provider' => ['required', 'string', 'max:100'], 'model' => ['required', 'string', 'max:150'], 'provenance' => ['sometimes', 'array']]);
        $draft = $service->draft($data['subject_type'], $data['subject_id'], $data['source_locale'], $data['target_locale'], $data['source_text'], $data['translated_text'], (float) $data['confidence'], $data['provider'], $data['model'], $data['provenance'] ?? []);
        return response()->json(['data' => TranslationDraftResource::make($draft)], 201);
    }

    public function update(Request $request, int|string $draft, TranslationAssistantQuery $query, TranslationAssistantService $service): JsonResponse
    {
        $model = $query->draft($draft); abort_unless($model, 404);
        $data = $request->validate(['translated_text' => ['sometimes', 'string'], 'confidence' => ['sometimes', 'numeric', 'between:0,1'], 'provenance' => ['sometimes', 'array']]);
        return response()->json(['data' => TranslationDraftResource::make($service->updateDraft($model, $data))]);
    }

    public function delete(int|string $draft, TranslationAssistantQuery $query, TranslationAssistantService $service): JsonResponse
    { $model = $query->draft($draft); abort_unless($model, 404); $service->removeDraft($model); return response()->json([], 204); }

    public function review(Request $request, int|string $draft, TranslationAssistantQuery $query, TranslationAssistantService $service): JsonResponse
    { $model = $query->draft($draft); abort_unless($model, 404); $data = $request->validate(['decision' => ['required', 'in:approved,rejected'], 'reviewer_type' => ['required', 'string', 'max:100'], 'reviewer_id' => ['required', 'string', 'max:100']]); return response()->json(['data' => TranslationDraftResource::make($service->review($model, $data['decision'], $data['reviewer_type'], $data['reviewer_id']))]); }

    public function check(int|string $draft, TranslationAssistantQuery $query, TranslationAssistantService $service): JsonResponse
    { $model = $query->draft($draft); abort_unless($model, 404); return response()->json(['data' => TranslationDraftResource::make($service->check($model))]); }

    public function glossary(Request $request, TranslationAssistantQuery $query): JsonResponse
    {
        $data = $request->validate(['source_locale' => ['sometimes', 'string', 'max:20'], 'target_locale' => ['sometimes', 'string', 'max:20']]);
        return response()->json(['data' => array_map(GlossaryEntryResource::make(...), $query->glossary($data['source_locale'] ?? null, $data['target_locale'] ?? null))]);
    }

    public function createGlossary(Request $request, TranslationAssistantService $service): JsonResponse
    {
        $data = $request->validate(['source_locale' => ['required', 'string', 'max:20'], 'target_locale' => ['required', 'string', 'max:20'], 'source_term' => ['required', 'string', 'max:255'], 'preferred_term' => ['required', 'string', 'max:255'], 'forbidden_terms' => ['sometimes', 'array']]);
        return response()->json(['data' => GlossaryEntryResource::make($service->addGlossary($data['source_locale'], $data['target_locale'], $data['source_term'], $data['preferred_term'], $data['forbidden_terms'] ?? []))], 201);
    }

    public function styleRules(Request $request, TranslationAssistantQuery $query): JsonResponse
    {
        $data = $request->validate(['locale' => ['sometimes', 'string', 'max:20']]);
        return response()->json(['data' => array_map(StyleRuleResource::make(...), $query->styleRules($data['locale'] ?? null))]);
    }

    public function createStyleRule(Request $request, TranslationAssistantService $service): JsonResponse
    {
        $data = $request->validate(['locale' => ['required', 'string', 'max:20'], 'name' => ['required', 'string', 'max:255'], 'pattern' => ['required', 'string'], 'message' => ['required', 'string'], 'severity' => ['sometimes', 'string', 'max:32']]);
        return response()->json(['data' => StyleRuleResource::make($service->addStyleRule($data['locale'], $data['name'], $data['pattern'], $data['message'], $data['severity'] ?? 'warning'))], 201);
    }
}

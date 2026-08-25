<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use Liberu\Cms\PollsAndSurveys\Services\PollService;

final class PollController
{
    public function show(string $key): JsonResponse
    {
        $poll = Poll::query()->where('key', $key)->where('active', true)->firstOrFail();

        return response()->json(['data' => ['key' => $poll->key, 'title' => $poll->title, 'questions' => $poll->questions->map(fn ($question): array => ['key' => $question->key, 'type' => $question->type, 'prompt' => $question->prompt, 'options' => $question->options, 'required' => $question->required])->all()]]);
    }

    public function store(Request $request, string $key, PollService $service): JsonResponse
    {
        $poll = Poll::query()->where('key', $key)->where('active', true)->firstOrFail();
        $data = $request->validate(['answers' => ['required', 'array'], 'respondent_key' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $response = $service->submit($poll, $data['answers'], $request->user()?->getAuthIdentifier(), $data['respondent_key'] ?? null);

        return response()->json(['data' => ['id' => $response->getKey(), 'submitted_at' => $response->submitted_at?->toISOString()]], 201);
    }
}

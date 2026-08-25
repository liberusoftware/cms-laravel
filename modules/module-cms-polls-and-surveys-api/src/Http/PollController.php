<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use Liberu\Cms\PollsAndSurveys\Services\PollService;

final class PollController
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'active' => ['sometimes', 'boolean']]);
        $polls = Poll::query()->when(array_key_exists('active', $data), fn ($query) => $query->where('active', $data['active']))->withCount('questions')->latest()->paginate((int) ($data['per_page'] ?? 25));

        return response()->json(['data' => $polls->through(fn (Poll $poll): array => $this->pollData($poll)), 'meta' => ['current_page' => $polls->currentPage(), 'last_page' => $polls->lastPage(), 'per_page' => $polls->perPage(), 'total' => $polls->total()]]);
    }

    public function create(Request $request, PollService $service): JsonResponse
    {
        $data = $request->validate($this->pollRules(true));

        return response()->json(['data' => $this->pollData($service->create($data, $request->user()?->current_team_id))], 201);
    }

    public function update(Request $request, string $key, PollService $service): JsonResponse
    {
        $poll = $this->managedPoll($key);

        return response()->json(['data' => $this->pollData($service->update($poll, $request->validate($this->pollRules(false))))]);
    }

    public function destroy(string $key): JsonResponse
    {
        $this->managedPoll($key)->delete();

        return response()->json(status: 204);
    }

    public function show(string $key): JsonResponse
    {
        $poll = Poll::query()->where('key', $key)->where('active', true)->firstOrFail();

        return response()->json(['data' => $this->pollData($poll, false)]);
    }

    public function store(Request $request, string $key, PollService $service): JsonResponse
    {
        $poll = Poll::query()->where('key', $key)->where('active', true)->firstOrFail();
        $data = $request->validate(['answers' => ['required', 'array'], 'respondent_key' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $response = $service->submit($poll, $data['answers'], $request->user()?->getAuthIdentifier(), $data['respondent_key'] ?? null);

        return response()->json(['data' => ['id' => $response->getKey(), 'submitted_at' => $response->submitted_at?->toISOString()]], 201);
    }

    public function results(string $key, PollService $service): JsonResponse
    {
        $poll = $this->managedPoll($key);
        abort_unless($poll->results_public || request()->user()?->can('polls-and-surveys.view'), 403);

        return response()->json(['data' => $service->results($poll)]);
    }

    public function export(string $key, PollService $service): JsonResponse
    {
        $poll = $this->managedPoll($key);
        abort_unless(request()->user()?->can('polls-and-surveys.export'), 403);

        return response()->json(['data' => $service->export($poll, (bool) request()->boolean('include_identity'))]);
    }

    public function eraseResponse(string $key, int $response, PollService $service): JsonResponse
    {
        $poll = $this->managedPoll($key);
        $record = $poll->responses()->whereKey($response)->firstOrFail();
        $service->eraseResponse($record);

        return response()->json(status: 204);
    }

    /** @return array<string, array<int, string>> */
    private function pollRules(bool $creating): array
    {
        return [
            'title' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'key' => [$creating ? 'required' : 'sometimes', 'string', 'regex:/^[a-z0-9][a-z0-9-]*$/', 'max:120'],
            'description' => ['sometimes', 'nullable', 'string'],
            'starts_at' => ['sometimes', 'nullable', 'date'],
            'ends_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:starts_at'],
            'allow_anonymous' => ['sometimes', 'boolean'],
            'allow_multiple' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
            'results_public' => ['sometimes', 'boolean'],
        ];
    }

    private function managedPoll(string $key): Poll
    {
        return Poll::query()->where('key', $key)->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function pollData(Poll $poll, bool $includeSchedule = true): array
    {
        $data = ['id' => $poll->getKey(), 'key' => $poll->key, 'title' => $poll->title, 'description' => $poll->description, 'active' => $poll->active, 'allow_anonymous' => $poll->allow_anonymous, 'allow_multiple' => $poll->allow_multiple, 'results_public' => $poll->results_public, 'questions' => $poll->questions->map(fn ($question): array => ['id' => $question->getKey(), 'key' => $question->key, 'type' => $question->type, 'prompt' => $question->prompt, 'options' => $question->options, 'branching' => $question->branching, 'position' => $question->position, 'required' => $question->required])->all()];
        if ($includeSchedule) {
            $data['starts_at'] = $poll->starts_at?->toISOString();
            $data['ends_at'] = $poll->ends_at?->toISOString();
        }

        return $data;
    }
}

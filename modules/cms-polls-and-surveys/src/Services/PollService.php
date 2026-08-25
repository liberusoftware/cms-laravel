<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use Liberu\Cms\PollsAndSurveys\Models\Question;
use Liberu\Cms\PollsAndSurveys\Models\Response;

final class PollService
{
    public function create(array $attributes, ?int $teamId = null): Poll
    {
        $this->validatePollAttributes($attributes);

        return Poll::create(array_merge($attributes, ['team_id' => $teamId ?? ($attributes['team_id'] ?? null)]));
    }

    public function update(Poll $poll, array $attributes): Poll
    {
        $this->validatePollAttributes($attributes, $poll);
        $poll->fill($attributes)->save();

        return $poll->refresh();
    }

    public function saveQuestion(Poll $poll, array $attributes, ?Question $question = null): Question
    {
        $this->validateQuestionAttributes($attributes, $question);

        if (! $question instanceof Question) {
            return $poll->questions()->create($attributes);
        }

        $question->fill($attributes)->save();

        return $question->refresh();
    }

    public function deleteQuestion(Question $question): void
    {
        $question->delete();
    }

    public function submit(Poll $poll, array $answers, ?int $userId = null, ?string $respondentKey = null): Response
    {
        if (! $poll->isOpen()) {
            throw ValidationException::withMessages(['poll' => 'This poll is not accepting responses.']);
        }
        if ($userId === null && ! $poll->allow_anonymous) {
            throw ValidationException::withMessages(['user' => 'Authentication is required for this poll.']);
        }

        $respondentHash = $respondentKey === null ? null : hash_hmac('sha256', $respondentKey, config('app.key'));
        if (! $poll->allow_multiple && ($userId !== null || $respondentHash !== null)) {
            $query = $poll->responses();
            $query->when($userId !== null, fn ($builder) => $builder->where('user_id', $userId));
            $query->when($userId === null, fn ($builder) => $builder->where('respondent_hash', $respondentHash));
            if ($query->exists()) {
                throw ValidationException::withMessages(['response' => 'A response has already been submitted.']);
            }
        }

        foreach ($poll->questions as $question) {
            if (! $this->questionIsVisible($question, $answers)) {
                continue;
            }

            if ($question->required && (! array_key_exists((string) $question->key, $answers) || $answers[$question->key] === null || $answers[$question->key] === '')) {
                throw ValidationException::withMessages(['answers.'.$question->key => 'This answer is required.']);
            }

            if (! array_key_exists((string) $question->key, $answers)) {
                continue;
            }

            $answer = $answers[$question->key];
            if ($question->type === 'multiple' && ! is_array($answer)) {
                throw ValidationException::withMessages(['answers.'.$question->key => 'Multiple-choice answers must be an array.']);
            }
            if ($question->options !== null) {
                $values = is_array($answer) ? $answer : [$answer];
                if (array_diff($values, $question->options) !== []) {
                    throw ValidationException::withMessages(['answers.'.$question->key => 'This answer is not valid.']);
                }
            }
        }

        return DB::transaction(fn (): Response => $poll->responses()->create([
            'user_id' => $userId,
            'respondent_hash' => $respondentHash,
            'answers' => $answers,
            'submitted_at' => now(),
            'team_id' => $poll->team_id,
        ]));
    }

    /** @return array<string, array<string, int>> */
    public function results(Poll $poll): array
    {
        $results = [];
        foreach ($poll->questions as $question) {
            $results[$question->key] = [];
            foreach ($poll->responses()->pluck('answers') as $answers) {
                $answer = $answers[$question->key] ?? null;
                foreach ((array) $answer as $value) {
                    $results[$question->key][(string) $value] = ($results[$question->key][(string) $value] ?? 0) + 1;
                }
            }
        }

        return $results;
    }

    /** @return list<array<string, mixed>> */
    public function export(Poll $poll, bool $includeRespondentIdentity = false): array
    {
        return $poll->responses()->orderBy('id')->get()->map(function (Response $response) use ($includeRespondentIdentity): array {
            $row = ['id' => $response->getKey(), 'answers' => $response->answers, 'submitted_at' => $response->submitted_at?->toISOString()];
            if ($includeRespondentIdentity) {
                $row['user_id'] = $response->user_id;
                $row['respondent_hash'] = $response->respondent_hash;
            }

            return $row;
        })->all();
    }

    public function eraseResponse(Response $response): void
    {
        $response->delete();
    }

    private function validatePollAttributes(array $attributes, ?Poll $poll = null): void
    {
        if (array_key_exists('title', $attributes) && trim((string) $attributes['title']) === '') {
            throw ValidationException::withMessages(['title' => 'A poll title is required.']);
        }
        if (array_key_exists('key', $attributes) && ! preg_match('/^[a-z0-9][a-z0-9-]*$/', (string) $attributes['key'])) {
            throw ValidationException::withMessages(['key' => 'The poll key must use lowercase letters, numbers, and hyphens.']);
        }
        if (! $poll instanceof Poll && ! array_key_exists('title', $attributes)) {
            throw ValidationException::withMessages(['title' => 'A poll title is required.']);
        }
        if (! $poll instanceof Poll && ! array_key_exists('key', $attributes)) {
            throw ValidationException::withMessages(['key' => 'A poll key is required.']);
        }
        if (isset($attributes['starts_at'], $attributes['ends_at']) && $attributes['starts_at'] !== null && $attributes['ends_at'] !== null && $attributes['ends_at'] < $attributes['starts_at']) {
            throw ValidationException::withMessages(['ends_at' => 'The end date must be after the start date.']);
        }
    }

    private function validateQuestionAttributes(array $attributes, ?Question $question = null): void
    {
        foreach (['key', 'prompt'] as $field) {
            if (array_key_exists($field, $attributes) && trim((string) $attributes[$field]) === '') {
                throw ValidationException::withMessages([$field => 'This field is required.']);
            }
        }
        if (! $question instanceof Question && (! isset($attributes['key']) || ! isset($attributes['prompt']))) {
            throw ValidationException::withMessages(['question' => 'A question key and prompt are required.']);
        }
        if (isset($attributes['type']) && ! in_array($attributes['type'], ['single', 'multiple', 'text', 'number'], true)) {
            throw ValidationException::withMessages(['type' => 'The question type is not supported.']);
        }
        if (isset($attributes['options']) && $attributes['options'] !== null && (! is_array($attributes['options']) || count($attributes['options']) !== count(array_unique($attributes['options'])))) {
            throw ValidationException::withMessages(['options' => 'Question options must be a unique list.']);
        }
        if (isset($attributes['branching']) && $attributes['branching'] !== null && ! is_array($attributes['branching'])) {
            throw ValidationException::withMessages(['branching' => 'Branching rules must be an array.']);
        }
        $key = (string) ($attributes['key'] ?? $question?->key);
        if ($key !== '' && isset($attributes['branching']) && $this->branchingReferences($attributes['branching'], $key) !== []) {
            throw ValidationException::withMessages(['branching' => 'A question cannot branch to itself.']);
        }
    }

    /** @return list<string> */
    private function branchingReferences(array $branching, string $questionKey): array
    {
        $references = [];
        array_walk_recursive($branching, function (mixed $value) use (&$references, $questionKey): void {
            if (is_string($value) && $value === $questionKey) {
                $references[] = $value;
            }
        });

        return $references;
    }

    private function questionIsVisible(Question $question, array $answers): bool
    {
        $branching = $question->branching;
        if (! is_array($branching) || $branching === []) {
            return true;
        }
        if (isset($branching['question'], $branching['equals'])) {
            return ($answers[$branching['question']] ?? null) === $branching['equals'];
        }

        return array_all($branching, fn ($expected, $source): bool => ! (($answers[$source] ?? null) !== $expected));
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveys\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use Liberu\Cms\PollsAndSurveys\Models\Response;

final class PollService
{
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
            if ($question->required && ! array_key_exists($question->key, $answers)) {
                throw ValidationException::withMessages(['answers.'.$question->key => 'This answer is required.']);
            }
            if (array_key_exists($question->key, $answers) && $question->options !== null && ! in_array($answers[$question->key], $question->options, true)) {
                throw ValidationException::withMessages(['answers.'.$question->key => 'This answer is not valid.']);
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
}

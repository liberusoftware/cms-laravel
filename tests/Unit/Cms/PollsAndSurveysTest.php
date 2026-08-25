<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use Liberu\Cms\PollsAndSurveys\Services\PollService;

uses(RefreshDatabase::class);

it('validates and stores a response while aggregating private results', function (): void {
    $poll = Poll::create(['title' => 'Feedback', 'key' => 'feedback', 'active' => true, 'allow_anonymous' => true]);
    $poll->questions()->create(['key' => 'rating', 'prompt' => 'Rating?', 'type' => 'single', 'options' => ['good', 'bad'], 'required' => true]);
    $service = app(PollService::class);

    $service->submit($poll, ['rating' => 'good'], null, 'anonymous-browser');

    expect($service->results($poll->fresh())['rating'])->toBe(['good' => 1]);
});

it('rejects duplicate and invalid responses', function (): void {
    $poll = Poll::create(['title' => 'Vote', 'key' => 'vote', 'active' => true, 'allow_anonymous' => false]);
    $poll->questions()->create(['key' => 'choice', 'prompt' => 'Choose', 'options' => ['a', 'b'], 'required' => true]);
    $service = app(PollService::class);
    expect(fn () => $service->submit($poll, ['choice' => 'x'], 7))->toThrow(ValidationException::class);
    $service->submit($poll, ['choice' => 'a'], 7);
    expect(fn () => $service->submit($poll, ['choice' => 'a'], 7))->toThrow(ValidationException::class);
});

it('honors poll schedules', function (): void {
    $poll = Poll::create(['title' => 'Closed', 'key' => 'closed', 'active' => true, 'starts_at' => now()->addDay()]);
    expect($poll->isOpen())->toBeFalse();
    expect(fn () => app(PollService::class)->submit($poll, [], null, 'subject'))->toThrow(ValidationException::class);
});

it('owns poll and question mutations and applies branching rules', function (): void {
    $service = app(PollService::class);
    $poll = $service->create(['title' => 'Survey', 'key' => 'survey', 'active' => true]);
    $service->saveQuestion($poll, ['key' => 'kind', 'prompt' => 'Kind?', 'options' => ['a', 'b'], 'required' => true]);
    $service->saveQuestion($poll, ['key' => 'detail', 'prompt' => 'Details', 'branching' => ['question' => 'kind', 'equals' => 'b'], 'required' => true]);

    $service->submit($poll->fresh(), ['kind' => 'a']);

    expect(fn () => $service->submit($poll->fresh(), ['kind' => 'b']))->toThrow(ValidationException::class);
});

it('exports responses without identity by default and erases them', function (): void {
    $poll = Poll::create(['title' => 'Privacy', 'key' => 'privacy', 'active' => true, 'allow_anonymous' => true]);
    $response = app(PollService::class)->submit($poll, [], null, 'browser');
    $service = app(PollService::class);

    expect($service->export($poll->fresh()))->toHaveCount(1)
        ->and($service->export($poll->fresh())[0])->not->toHaveKey('respondent_hash');
    $service->eraseResponse($response);
    expect($poll->responses()->count())->toBe(0);
});

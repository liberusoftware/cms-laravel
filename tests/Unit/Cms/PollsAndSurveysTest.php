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

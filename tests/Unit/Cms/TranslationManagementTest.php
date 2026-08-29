<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\TranslationManagement\Actions\TranslationManagementService;
use Liberu\Cms\TranslationManagement\Contracts\TranslationVendorInterface;
use Liberu\Cms\TranslationManagement\Events\SourceChangeTranslated;
use Liberu\Cms\TranslationManagement\Events\TranslationJobReconciled;
use Liberu\Cms\TranslationManagement\Models\TranslationMemory;
use Liberu\Cms\TranslationManagement\Support\TranslationResult;
use Liberu\Cms\TranslationManagement\Support\TranslationVendorRegistry;

uses(RefreshDatabase::class);

final class FakeTranslationVendor implements TranslationVendorInterface
{
    public function key(): string
    {
        return 'fake';
    }

    public function translate(string $source, string $sourceLocale, string $targetLocale, array $context = []): TranslationResult
    {
        return new TranslationResult("{$targetLocale}: {$source}", 'fake', 'fake-v1', 0.25, ['request_id' => 'test']);
    }
}

final class FailingTranslationVendor implements TranslationVendorInterface
{
    public function key(): string
    {
        return 'failing';
    }

    public function translate(string $source, string $sourceLocale, string $targetLocale, array $context = []): TranslationResult
    {
        throw new \RuntimeException('translation provider unavailable');
    }
}

it('runs a translation job through source capture, vendor translation, review, memory, and reconciliation', function (): void {
    Event::fake();
    app(TranslationVendorRegistry::class)->register(new FakeTranslationVendor);
    $service = app(TranslationManagementService::class);

    $job = $service->createJob(['name' => 'Website copy', 'source_locale' => 'en', 'target_locale' => 'fr']);
    $change = $service->addSourceChange($job, ['subject_type' => 'page', 'subject_id' => '42', 'field' => 'title', 'source_text' => 'Welcome']);
    $duplicate = $service->addSourceChange($job, ['subject_type' => 'page', 'subject_id' => '42', 'field' => 'title', 'source_text' => 'Welcome']);
    $translated = $service->translate($change, 'fake');
    $reviewed = $service->review($translated, 'approved', 'Looks good');
    $reconciled = $service->reconcile($job->refresh());

    expect($duplicate->is($change))->toBeTrue()
        ->and($reviewed->status)->toBe('approved')
        ->and($reconciled->status)->toBe('completed')
        ->and($reconciled->completed_units)->toBe(1)
        ->and($reconciled->actual_cost)->toBe(0.25)
        ->and(TranslationMemory::query()->where('source_text', 'Welcome')->exists())->toBeTrue();
    Event::assertDispatched(SourceChangeTranslated::class);
    Event::assertDispatched(TranslationJobReconciled::class);
});

it('rejects invalid locale pairs, roles, and review of untranslated content', function (): void {
    $service = app(TranslationManagementService::class);

    expect(fn () => $service->createJob(['name' => 'Invalid', 'source_locale' => 'en', 'target_locale' => 'en']))->toThrow(ValidationException::class);

    $job = $service->createJob(['name' => 'Valid', 'source_locale' => 'en', 'target_locale' => 'de']);
    $change = $service->addSourceChange($job, ['subject_type' => 'page', 'subject_id' => '1', 'field' => 'body', 'source_text' => 'Hello']);

    expect(fn () => $service->assign($change, 'user', 1, 'owner'))->toThrow(ValidationException::class)
        ->and(fn () => $service->review($change, 'approved'))->toThrow(ValidationException::class);
});

it('records vendor failures on the source change and job', function (): void {
    app(TranslationVendorRegistry::class)->register(new FailingTranslationVendor);
    $service = app(TranslationManagementService::class);
    $job = $service->createJob(['name' => 'Failure', 'source_locale' => 'en', 'target_locale' => 'fr']);
    $change = $service->addSourceChange($job, ['subject_type' => 'page', 'subject_id' => '2', 'field' => 'title', 'source_text' => 'Hello']);

    expect(fn () => $service->translate($change, 'failing'))->toThrow(\RuntimeException::class);
    expect($change->refresh()->status)->toBe('failed')->and($job->refresh()->status)->toBe('failed');
});

it('cancels an active job and rejects cancellation after completion', function (): void {
    $service = app(TranslationManagementService::class);
    $job = $service->createJob(['name' => 'Cancel me', 'source_locale' => 'en', 'target_locale' => 'fr']);

    expect($service->cancel($job)->status)->toBe('cancelled');
    expect(fn () => $service->cancel($job->refresh()))->toThrow(ValidationException::class);
});

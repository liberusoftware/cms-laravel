<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Form\FormSubmitted;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Observability\Metrics\LogMetricsRecorder;
use Liberu\Cms\Observability\Metrics\NullMetricsRecorder;
use Tests\Fixtures\RecordingMetricsRecorder;

uses(RefreshDatabase::class);

it('binds the log recorder by default', function (): void {
    expect(app(MetricsRecorderInterface::class))->toBeInstanceOf(LogMetricsRecorder::class);
});

it('binds the null recorder when metrics are disabled', function (): void {
    config(['cms-observability.metrics.enabled' => false]);
    app()->forgetInstance(MetricsRecorderInterface::class);

    expect(app(MetricsRecorderInterface::class))->toBeInstanceOf(NullMetricsRecorder::class);
});

it('increments a domain counter when its event crosses the bus', function (): void {
    $recorder = new RecordingMetricsRecorder;
    app()->instance(MetricsRecorderInterface::class, $recorder);

    $bus = app(EventBusInterface::class);
    $bus->dispatch(new ContentPublished('page', 1));
    $bus->dispatch(new FormSubmitted('contact', 1, null, []));

    expect($recorder->names())->toContain('content.published', 'form.submitted');
});

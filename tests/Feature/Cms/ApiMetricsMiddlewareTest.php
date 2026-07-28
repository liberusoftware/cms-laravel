<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Tests\Fixtures\RecordingMetricsRecorder;

function bindMetricsSpy(): RecordingMetricsRecorder
{
    $spy = new RecordingMetricsRecorder;
    app()->instance(MetricsRecorderInterface::class, $spy);

    return $spy;
}

it('records count and latency for an api/v1 request, tagged by route and status', function (): void {
    $spy = bindMetricsSpy();

    Route::get('api/v1/_probe', fn (): string => 'ok')->name('cms-test.probe');

    $this->getJson('/api/v1/_probe')->assertOk();

    expect($spy->methods())->toContain('increment')->toContain('timing')
        ->and($spy->names())->each->toBe('api.request')
        ->and($spy->calls[0]['tags'])->toBe(['route' => 'cms-test.probe', 'status' => 200]);
});

it('records nothing for a web request', function (): void {
    $spy = bindMetricsSpy();

    Route::get('_probe/web/ping', fn (): string => 'ok');

    $this->get('/_probe/web/ping')->assertOk();

    expect($spy->calls)->toBe([]);
});

it('records nothing for the readiness probe or the liveness probe', function (): void {
    $spy = bindMetricsSpy();

    $this->getJson('/health/ready')->assertOk();
    $this->get('/up')->assertOk();

    expect($spy->calls)->toBe([]);
});

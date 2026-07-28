<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Pages\Models\Page;
use Tests\Fixtures\RecordingMetricsRecorder;

uses(RefreshDatabase::class);

it('records query count, latency, and result count for a search', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');

    Page::factory()->published()->create(['team_id' => $team->id, 'title' => 'Laravel Guide', 'slug' => 'laravel-guide', 'content' => 'x']);

    $spy = new RecordingMetricsRecorder;
    app()->instance(MetricsRecorderInterface::class, $spy);

    $this->getJson('/api/v1/search?q=Laravel')->assertOk();

    expect($spy->names())->toContain('search.query', 'search.latency', 'search.results');

    $results = collect($spy->calls)->firstWhere('name', 'search.results');
    expect($results['value'])->toBe(1.0);
});

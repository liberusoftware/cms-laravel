<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\TranslationManagement\Models\TranslationJob;

uses(RefreshDatabase::class);

it('lists translation jobs and creates source changes through the versioned API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
    $job = TranslationJob::create(['name' => 'Docs', 'source_locale' => 'en', 'target_locale' => 'de', 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/translation-management/jobs')
        ->assertOk()
        ->assertJsonPath('data.0.type', 'cms-translation-job')
        ->assertJsonPath('data.0.id', $job->public_id);

    $this->postJson("/api/v1/cms/translation-management/jobs/{$job->public_id}/source-changes", [
        'subject_type' => 'page', 'subject_id' => '7', 'field' => 'title', 'source_text' => 'Welcome',
    ])->assertCreated()
        ->assertJsonPath('data.type', 'cms-translation-source-change')
        ->assertJsonPath('data.status', 'pending');
});

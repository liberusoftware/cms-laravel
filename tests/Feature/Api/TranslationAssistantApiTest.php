<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\TranslationAssistant\Models\TranslationDraft;

uses(RefreshDatabase::class);

it('lists and creates translation assistant drafts through the versioned API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
    TranslationDraft::create(['subject_type' => 'page', 'subject_id' => '1', 'source_locale' => 'en', 'target_locale' => 'de', 'source_text' => 'Hello', 'translated_text' => 'Hallo', 'confidence' => .9, 'status' => 'draft', 'provider' => 'test', 'model' => 'test', 'provenance' => [], 'violations' => [], 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/translation-assistant')
        ->assertOk()
        ->assertJsonPath('data.0.source_locale', 'en')
        ->assertJsonPath('data.0.target_locale', 'de');

    $this->postJson('/api/v1/cms/translation-assistant', ['subject_type' => 'page', 'subject_id' => '2', 'source_locale' => 'en', 'target_locale' => 'fr', 'source_text' => 'Hello', 'translated_text' => 'Bonjour', 'confidence' => .95, 'provider' => 'test', 'model' => 'test'])
        ->assertCreated()
        ->assertJsonPath('data.status', 'draft')
        ->assertJsonPath('data.type', 'cms-translation-draft');
});

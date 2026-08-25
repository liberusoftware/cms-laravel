<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;

uses(RefreshDatabase::class);

it('returns explicit playback metadata through the video and audio API', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
    $asset = MediaAsset::create(['title' => 'Podcast', 'kind' => 'audio', 'source_type' => 'remote', 'source_uri' => 'https://cdn.example.test/podcast.mp3', 'stream_uri' => 'https://stream.example.test/podcast.mp3', 'status' => 'ready', 'team_id' => $team->id]);

    $this->getJson("/api/v1/cms/video-and-audio/assets/{$asset->public_id}/playback")
        ->assertOk()
        ->assertJsonPath('data.type', 'cms-playback-metadata')
        ->assertJsonPath('data.kind', 'audio')
        ->assertJsonPath('data.stream_uri', 'https://stream.example.test/podcast.mp3');
});

it('updates and archives media through the domain mutation boundary', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
    $asset = MediaAsset::create(['title' => 'Draft', 'kind' => 'audio', 'source_type' => 'upload', 'source_uri' => 'audio.mp3', 'team_id' => $team->id]);

    $this->patchJson("/api/v1/cms/video-and-audio/assets/{$asset->public_id}", ['title' => 'Updated'])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated');
    $this->postJson("/api/v1/cms/video-and-audio/assets/{$asset->public_id}/archive")
        ->assertOk()
        ->assertJsonPath('data.status', 'archived');
});

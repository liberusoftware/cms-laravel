<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Media\Media\StoreUpload;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('public');
    $this->team = Team::factory()->create();
    Sanctum::actingAs($this->team, ['content:read', 'media:write'], 'sanctum');
});

it('lists and uploads tenant-scoped media through the API', function (): void {
    $uploaded = $this->postJson('/api/v1/cms/media-library/upload', [
        'file' => UploadedFile::fake()->image('cover.jpg'),
        'folder' => 'gallery',
    ])->assertCreated();

    $this->getJson('/api/v1/cms/media-library?folder=gallery')
        ->assertOk()
        ->assertJsonPath('data.0.id', $uploaded->json('data.id'))
        ->assertJsonPath('data.0.type', 'cms-media-library');

    $this->postJson('/api/v1/cms/media-library/upload', [
        'file' => UploadedFile::fake()->image('new.jpg'),
        'folder' => 'gallery',
    ])->assertCreated()->assertJsonPath('data.attributes.file_name', 'new.jpg');
});

it('does not expose media belonging to another tenant', function (): void {
    $other = Team::withoutEvents(fn (): Team => Team::factory()->create());
    Sanctum::actingAs($other, ['content:read'], 'sanctum');
    app(StoreUpload::class)(UploadedFile::fake()->image('private.jpg'));

    Sanctum::actingAs($this->team, ['content:read'], 'sanctum');

    $this->getJson('/api/v1/cms/media-library')->assertOk()->assertJsonCount(0, 'data');
});

it('deletes media through the API', function (): void {
    $media = $this->postJson('/api/v1/cms/media-library/upload', [
        'file' => UploadedFile::fake()->image('remove.jpg'),
    ])->assertCreated()->json('data');

    $this->deleteJson('/api/v1/cms/media-library/'.$media['id'])
        ->assertNoContent();

    $this->assertDatabaseMissing('cms_media', ['id' => $media['id']]);
});

<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;
use Liberu\Cms\VideoAndAudioFilament\Resources\MediaAssetResource;
use Liberu\Cms\VideoAndAudioLivewire\VideoAndAudioLivewireServiceProvider;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('registers the media Livewire browser', function (): void {
    app()->register(VideoAndAudioLivewireServiceProvider::class);
    MediaAsset::create(['title' => 'Podcast', 'kind' => 'audio', 'source_type' => 'remote', 'source_uri' => 'https://cdn.example.test/podcast.mp3']);
    expect(app('livewire')->exists('module-cms-video-and-audio::media-browser'))->toBeTrue();
    Livewire::test('module-cms-video-and-audio::media-browser')->assertSuccessful()->assertSee('Podcast');
});

it('exposes the media asset Filament resource contract', function (): void {
    expect(MediaAssetResource::getModel())->toBe(MediaAsset::class)->and(MediaAssetResource::getPages())->toHaveKey('index');
});

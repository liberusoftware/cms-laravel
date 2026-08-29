<?php

declare(strict_types=1);

namespace Tests\Unit\Cms;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\VideoAndAudio\Actions\MediaManagementService;
use Liberu\Cms\VideoAndAudio\Contracts\TranscodingAdapterInterface;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;
use Liberu\Cms\VideoAndAudio\Support\TranscodeResult;
use Liberu\Cms\VideoAndAudio\Support\TranscodingAdapterRegistry;

uses(RefreshDatabase::class);

final class FakeTranscodingAdapter implements TranscodingAdapterInterface
{
    public function key(): string
    {
        return 'fake';
    }

    public function transcode(string $sourceUri, string $profile, array $context = []): TranscodeResult
    {
        return new TranscodeResult("{$sourceUri}.{$profile}.mp4", 123, ['profile' => $profile]);
    }
}

final class FailingTranscodingAdapter implements TranscodingAdapterInterface
{
    public function key(): string
    {
        return 'failing';
    }

    public function transcode(string $sourceUri, string $profile, array $context = []): TranscodeResult
    {
        throw new \RuntimeException('transcoder unavailable');
    }
}

it('manages remote media, tracks, transcoding, idempotency, and playback metadata', function (): void {
    app(TranscodingAdapterRegistry::class)->register(new FakeTranscodingAdapter);
    $service = app(MediaManagementService::class);
    $asset = $service->createAsset(['title' => 'Launch video', 'kind' => 'video', 'source_type' => 'remote', 'source_uri' => 'https://cdn.example.test/launch.mp4', 'duration_seconds' => 30]);
    $service->addTrack($asset, ['track_type' => 'caption', 'language' => 'en', 'label' => 'English', 'content' => 'Hello']);
    $service->addTrack($asset, ['track_type' => 'chapter', 'start_seconds' => 0, 'end_seconds' => 10, 'label' => 'Intro']);
    $variant = $service->transcode($asset, 'fake', 'web', 'asset-1');
    $same = $service->transcode($asset->refresh(), 'fake', 'web', 'asset-1');
    $playback = $service->playback($asset->refresh()->load('tracks'));

    expect($variant->status)->toBe('ready')->and($same->is($variant))->toBeTrue()->and($playback->streamUri)->toBe('https://cdn.example.test/launch.mp4.web.mp4')->and($playback->tracks)->toHaveCount(2);
});

it('rejects invalid media sources and chapter ranges', function (): void {
    $service = app(MediaManagementService::class);
    expect(fn () => $service->createAsset(['title' => 'Bad', 'kind' => 'video', 'source_type' => 'remote', 'source_uri' => 'not-a-url']))->toThrow(ValidationException::class);
    $asset = MediaAsset::create(['title' => 'Audio', 'kind' => 'audio', 'source_type' => 'upload', 'source_uri' => 'audio.mp3']);
    expect(fn () => $service->addTrack($asset, ['track_type' => 'chapter', 'start_seconds' => 10, 'end_seconds' => 5]))->toThrow(ValidationException::class);
});

it('records a failed transcoding variant for recovery', function (): void {
    app(TranscodingAdapterRegistry::class)->register(new FailingTranscodingAdapter);
    $asset = app(MediaManagementService::class)->createAsset(['title' => 'Retry me', 'kind' => 'video', 'source_type' => 'upload', 'source_uri' => 'video.mp4']);

    expect(fn () => app(MediaManagementService::class)->transcode($asset, 'failing', 'web', 'failure-1'))
        ->toThrow(\RuntimeException::class);
    expect($asset->variants()->first()?->status)->toBe('failed');
});

it('updates and removes tracks through the media boundary', function (): void {
    $service = app(MediaManagementService::class);
    $asset = $service->createAsset(['title' => 'Track lifecycle', 'kind' => 'audio', 'source_type' => 'upload', 'source_uri' => 'audio.mp3']);
    $track = $service->addTrack($asset, ['track_type' => 'caption', 'language' => 'en', 'content' => 'Draft']);

    expect($service->updateTrack($track, ['content' => 'Final'])->content)->toBe('Final');
    $service->deleteTrack($track->fresh());

    expect($asset->tracks()->count())->toBe(0);
});

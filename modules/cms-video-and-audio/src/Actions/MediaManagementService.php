<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\VideoAndAudio\Events\MediaAssetCreated;
use Liberu\Cms\VideoAndAudio\Events\MediaTranscoded;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;
use Liberu\Cms\VideoAndAudio\Models\MediaTrack;
use Liberu\Cms\VideoAndAudio\Models\MediaVariant;
use Liberu\Cms\VideoAndAudio\Support\PlaybackMetadata;
use Liberu\Cms\VideoAndAudio\Support\TranscodingAdapterRegistry;

final readonly class MediaManagementService
{
    public function __construct(private TranscodingAdapterRegistry $adapters) {}

    public function createAsset(array $attributes): MediaAsset
    {
        if (! in_array($attributes['kind'] ?? null, config('video-and-audio.asset_kinds', []), true)) {
            throw ValidationException::withMessages(['kind' => 'Choose video or audio.']);
        }
        if (! in_array($attributes['source_type'] ?? null, ['upload', 'remote'], true) || blank($attributes['source_uri'] ?? null)) {
            throw ValidationException::withMessages(['source_uri' => 'A valid upload or remote source is required.']);
        }
        if (($attributes['source_type'] ?? null) === 'remote' && filter_var($attributes['source_uri'], FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['source_uri' => 'Remote sources must be valid URLs.']);
        }
        if (isset($attributes['bytes']) && (int) $attributes['bytes'] > (int) config('video-and-audio.max_upload_bytes', 5368709120)) {
            throw ValidationException::withMessages(['bytes' => 'The media source exceeds the configured size limit.']);
        }

        $asset = DB::transaction(fn (): MediaAsset => MediaAsset::create([...$attributes, 'status' => $attributes['status'] ?? 'draft']));
        event(new MediaAssetCreated($asset));

        return $asset;
    }

    public function addTrack(MediaAsset $asset, array $attributes): MediaTrack
    {
        $type = $attributes['track_type'] ?? null;
        if (! in_array($type, config('video-and-audio.track_types', []), true)) {
            throw ValidationException::withMessages(['track_type' => 'Unsupported media track type.']);
        }
        if ($type === 'chapter' && ((float) ($attributes['end_seconds'] ?? 0) <= (float) ($attributes['start_seconds'] ?? 0))) {
            throw ValidationException::withMessages(['end_seconds' => 'Chapter end must be after its start.']);
        }
        if (in_array($type, ['caption', 'transcript'], true) && blank($attributes['uri'] ?? $attributes['content'] ?? null)) {
            throw ValidationException::withMessages(['content' => 'Captions and transcripts require a URI or content.']);
        }

        return $asset->tracks()->create([...$attributes, 'team_id' => $attributes['team_id'] ?? $asset->team_id]);
    }

    public function updateAsset(MediaAsset $asset, array $attributes): MediaAsset
    {
        if ($asset->status === 'archived') {
            throw ValidationException::withMessages(['status' => 'Archived media cannot be updated.']);
        }
        if (array_key_exists('source_uri', $attributes) && blank($attributes['source_uri'])) {
            throw ValidationException::withMessages(['source_uri' => 'A media source is required.']);
        }
        $asset->update(array_intersect_key($attributes, array_flip(['title', 'source_uri', 'mime_type', 'bytes', 'duration_seconds', 'stream_uri', 'poster_uri', 'status', 'metadata', 'checksum'])));

        return $asset->refresh();
    }

    public function archive(MediaAsset $asset): MediaAsset
    {
        $asset->update(['status' => 'archived']);

        return $asset->refresh();
    }

    public function transcode(MediaAsset $asset, string $adapter, string $profile, string $idempotencyKey, array $context = []): MediaVariant
    {
        if ($asset->status === 'archived') {
            throw ValidationException::withMessages(['status' => 'Archived media cannot be transcoded.']);
        }
        if (trim($idempotencyKey) === '') {
            throw ValidationException::withMessages(['idempotency_key' => 'An idempotency key is required.']);
        }
        $variant = $asset->variants()->firstOrCreate(['idempotency_key' => $idempotencyKey], ['adapter' => $adapter, 'profile' => $profile, 'status' => 'processing', 'team_id' => $asset->team_id]);
        if ($variant->status === 'ready') {
            return $variant;
        }
        try {
            $result = $this->adapters->resolve($adapter)->transcode($asset->source_uri, $profile, $context);
            $variant->update(['adapter' => $adapter, 'profile' => $profile, 'uri' => $result->uri, 'bytes' => $result->bytes, 'metadata' => $result->metadata, 'status' => 'ready', 'failure_reason' => null]);
            $asset->update(['status' => 'ready', 'stream_uri' => $asset->stream_uri ?: $result->uri]);
        } catch (\Throwable $exception) {
            $variant->update(['status' => 'failed', 'failure_reason' => $exception->getMessage()]);
            throw $exception;
        }
        $result = $variant->refresh();
        event(new MediaTranscoded($result));

        return $result;
    }

    public function playback(MediaAsset $asset): PlaybackMetadata
    {
        if ($asset->status !== 'ready') {
            throw ValidationException::withMessages(['status' => 'Media is not ready for playback.']);
        }

        return new PlaybackMetadata($asset->public_id, $asset->kind, $asset->title, $asset->stream_uri, $asset->poster_uri, $asset->duration_seconds, $asset->tracks()->where('status', 'active')->get()->map(fn (MediaTrack $track): array => ['type' => $track->track_type, 'language' => $track->language, 'label' => $track->label, 'uri' => $track->uri, 'content' => $track->content, 'start_seconds' => $track->start_seconds, 'end_seconds' => $track->end_seconds])->all(), $asset->metadata ?? []);
    }
}

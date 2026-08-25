<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Liberu\Cms\VideoAndAudio\Actions\MediaManagementService;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;
use Liberu\Cms\VideoAndAudio\Queries\MediaAssetQuery;
use Liberu\Cms\VideoAndAudioApi\Http\Resources\MediaAssetResource;
use Liberu\Cms\VideoAndAudioApi\Http\Resources\MediaTrackResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class VideoAndAudioController
{
    public function __construct(private readonly MediaAssetQuery $assets, private readonly MediaManagementService $service) {}
    public function index(Request $request): AnonymousResourceCollection { return MediaAssetResource::collection($this->assets->paginate($request->integer('per_page', 15), (string) $request->string('search'), $request->string('kind')->toString() ?: null)); }
    public function show(string $publicId): MediaAssetResource { $asset = $this->assets->find($publicId); if (! $asset) throw new NotFoundHttpException; return new MediaAssetResource($asset); }
    public function create(Request $request): MediaAssetResource { $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'kind' => ['required', 'in:video,audio'], 'source_type' => ['required', 'in:upload,remote'], 'source_uri' => ['required', 'string'], 'mime_type' => ['nullable', 'string', 'max:255'], 'bytes' => ['nullable', 'integer', 'min:0'], 'duration_seconds' => ['nullable', 'integer', 'min:0'], 'metadata' => ['nullable', 'array']]); return new MediaAssetResource($this->service->createAsset($data)); }
    public function update(Request $request, string $publicId): MediaAssetResource { $asset = $this->assets->find($publicId); if (! $asset) throw new NotFoundHttpException; $data = $request->validate(['title' => ['sometimes', 'string', 'max:255'], 'source_uri' => ['sometimes', 'string'], 'mime_type' => ['nullable', 'string', 'max:255'], 'bytes' => ['nullable', 'integer', 'min:0'], 'duration_seconds' => ['nullable', 'integer', 'min:0'], 'stream_uri' => ['nullable', 'string'], 'poster_uri' => ['nullable', 'string'], 'status' => ['sometimes', 'string', 'max:32'], 'metadata' => ['nullable', 'array'], 'checksum' => ['nullable', 'string', 'size:64']]); return new MediaAssetResource($this->service->updateAsset($asset, $data)); }
    public function archive(string $publicId): MediaAssetResource { $asset = $this->assets->find($publicId); if (! $asset) throw new NotFoundHttpException; return new MediaAssetResource($this->service->archive($asset)); }
    public function track(Request $request, string $publicId): MediaTrackResource { $asset = $this->assets->find($publicId); if (! $asset) throw new NotFoundHttpException; $data = $request->validate(['track_type' => ['required', 'in:poster,chapter,caption,transcript'], 'language' => ['nullable', 'string', 'max:16'], 'label' => ['nullable', 'string', 'max:255'], 'uri' => ['nullable', 'string'], 'content' => ['nullable', 'string'], 'start_seconds' => ['nullable', 'numeric', 'min:0'], 'end_seconds' => ['nullable', 'numeric', 'min:0'], 'metadata' => ['nullable', 'array']]); return new MediaTrackResource($this->service->addTrack($asset, $data)); }
    public function playback(string $publicId): JsonResponse { $asset = $this->assets->find($publicId); if (! $asset) throw new NotFoundHttpException; $playback = $this->service->playback($asset); return response()->json(['data' => ['id' => $playback->publicId, 'type' => 'cms-playback-metadata', 'kind' => $playback->kind, 'title' => $playback->title, 'stream_uri' => $playback->streamUri, 'poster_uri' => $playback->posterUri, 'duration_seconds' => $playback->durationSeconds, 'tracks' => $playback->tracks, 'metadata' => $playback->metadata]]); }
    public function transcode(Request $request, string $publicId): JsonResponse { $asset = $this->assets->find($publicId); if (! $asset) throw new NotFoundHttpException; $data = $request->validate(['adapter' => ['required', 'string', 'max:255'], 'profile' => ['required', 'string', 'max:255'], 'idempotency_key' => ['required', 'string', 'max:255'], 'context' => ['nullable', 'array']]); $variant = $this->service->transcode($asset, $data['adapter'], $data['profile'], $data['idempotency_key'], $data['context'] ?? []); return response()->json(['data' => ['id' => (string) $variant->getKey(), 'type' => 'cms-media-variant', 'status' => $variant->status, 'uri' => $variant->uri, 'profile' => $variant->profile]], 202); }
}

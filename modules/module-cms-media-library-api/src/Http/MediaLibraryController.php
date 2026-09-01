<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Media\Media\StoreUpload;
use Liberu\Cms\MediaLibraryApi\Http\Resources\MediaItemResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class MediaLibraryController
{
    public function __construct(private MediaRepositoryInterface $media) {}

    public function index(Request $request): JsonResponse
    {
        $folder = $request->input('folder');
        $folder = is_string($folder) && trim($folder) !== '' ? trim($folder) : null;
        $size = max(1, min($request->integer('page_size', 25), 100));
        $items = array_slice(iterator_to_array($this->media->inFolder($folder)), 0, $size);

        return response()->json(['data' => array_map(
            static fn ($item): array => (new MediaItemResource($item))->resolve($request),
            $items,
        )]);
    }

    public function show(int|string $media, Request $request): MediaItemResource
    {
        $item = $this->media->find($media);
        if ($item === null) {
            throw new NotFoundHttpException;
        }

        return new MediaItemResource($item);
    }

    public function upload(Request $request, StoreUpload $store): JsonResponse
    {
        $request->validate(['file' => ['required', 'file'], 'folder' => ['nullable', 'string', 'max:255']]);
        $file = $request->file('file');
        if (! $file instanceof UploadedFile) {
            throw new NotFoundHttpException;
        }

        $folder = $request->input('folder');
        $item = $store($file, is_string($folder) ? $folder : null);

        return response()->json(['data' => (new MediaItemResource($item))->resolve($request)], 201);
    }

    public function destroy(int|string $media): JsonResponse
    {
        abort_unless($this->media->delete($media), 404);

        return response()->json(status: 204);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\EventsContent\Queries\EventsContentQuery;
use Liberu\Cms\EventsContent\Services\EventsContentService;
use Liberu\Cms\EventsContentApi\Http\Resources\EventResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EventsContentController
{
    public function index(Request $request, EventsContentQuery $query): JsonResponse
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string', 'max:255'], 'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'], 'include_archived' => ['sometimes', 'boolean']]);
        $events = $query->calendar((int) ($data['per_page'] ?? 15), (string) ($data['search'] ?? ''), (bool) ($data['include_archived'] ?? false));

        return response()->json(['data' => EventResource::collection($events->getCollection()), 'meta' => ['current_page' => $events->currentPage(), 'last_page' => $events->lastPage(), 'per_page' => $events->perPage(), 'total' => $events->total()]]);
    }

    public function show(string $key, EventsContentQuery $query): EventResource
    {
        $event = $query->find($key);
        if (! $event || $event->status !== 'published') {
            throw new NotFoundHttpException;
        }

        return new EventResource($event);
    }

    public function store(Request $request, EventsContentService $service): EventResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'starts_at' => ['required', 'date'], 'ends_at' => ['required', 'date'], 'timezone' => ['sometimes', 'timezone'], 'venue_id' => ['nullable', 'integer']]);

        return new EventResource($service->event($data, $request->user()?->current_team_id));
    }

    public function publish(string $key, EventsContentQuery $query, EventsContentService $service): EventResource
    {
        $event = $query->find($key);
        if (! $event) {
            throw new NotFoundHttpException;
        }

        return new EventResource($service->publish($event));
    }

    public function archive(string $key, EventsContentQuery $query, EventsContentService $service): EventResource
    {
        $event = $query->find($key);
        if (! $event) {
            throw new NotFoundHttpException;
        }

        return new EventResource($service->archive($event));
    }
}

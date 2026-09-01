<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentCalendar\Models\CalendarItem;
use Liberu\Cms\ContentCalendar\Services\ContentCalendarService;

final class ContentCalendarController
{
    public function index(Request $request, ContentCalendarService $service): JsonResponse
    {
        return response()->json(['data' => $service->items($request->user()?->current_team_id, $request->string('channel')->toString() ?: null, $request->string('site')->toString() ?: null, $request->integer('page_size', 25))]);
    }

    public function campaign(Request $request, ContentCalendarService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'slug' => ['nullable', 'string'], 'description' => ['nullable', 'string'], 'status' => ['nullable', 'string']]);

        return response()->json(['data' => $service->campaign($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function store(Request $request, ContentCalendarService $service): JsonResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:200'], 'content_type' => ['nullable', 'string'], 'subject_key' => ['nullable', 'string'], 'channel' => ['nullable', 'string'], 'site' => ['nullable', 'string'], 'starts_at' => ['required', 'date'], 'deadline_at' => ['nullable', 'date'], 'assigned_to' => ['nullable', 'integer']]);

        return response()->json(['data' => $service->schedule($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function reschedule(Request $request, CalendarItem $item, ContentCalendarService $service): JsonResponse
    {
        $raw = $request->validate(['starts_at' => ['required', 'date'], 'deadline_at' => ['nullable', 'date']]);
        $data = is_array($raw) ? $raw : [];

        abort_unless($item->team_id === $request->user()?->current_team_id, 404);

        return response()->json(['data' => $service->reschedule($item, is_string($data['starts_at'] ?? null) ? $data['starts_at'] : '', is_string($data['deadline_at'] ?? null) ? $data['deadline_at'] : null)]);
    }

    /** @return array<string, mixed> */
    private function normalized(mixed $value): array
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_string($key)) {
                    $data[$key] = $item;
                }
            }
        }

        return $data;
    }
}

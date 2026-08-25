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
        return response()->json(['data' => $service->items($request->user()?->current_team_id, $request->input('channel'), $request->input('site'), $request->integer('page_size', 25))]);
    }

    public function campaign(Request $request, ContentCalendarService $service): JsonResponse
    {
        return response()->json(['data' => $service->campaign($request->validate(['name' => ['required', 'string', 'max:160'], 'slug' => ['nullable', 'string'], 'description' => ['nullable', 'string'], 'status' => ['nullable', 'string']]), $request->user()?->current_team_id)], 201);
    }

    public function store(Request $request, ContentCalendarService $service): JsonResponse
    {
        return response()->json(['data' => $service->schedule($request->validate(['title' => ['required', 'string', 'max:200'], 'content_type' => ['nullable', 'string'], 'subject_key' => ['nullable', 'string'], 'channel' => ['nullable', 'string'], 'site' => ['nullable', 'string'], 'starts_at' => ['required', 'date'], 'deadline_at' => ['nullable', 'date'], 'assigned_to' => ['nullable', 'integer']]), $request->user()?->current_team_id)], 201);
    }

    public function reschedule(Request $request, CalendarItem $item, ContentCalendarService $service): JsonResponse
    {
        $data = $request->validate(['starts_at' => ['required', 'date'], 'deadline_at' => ['nullable', 'date']]);

        return response()->json(['data' => $service->reschedule($item, $data['starts_at'], $data['deadline_at'] ?? null)]);
    }
}

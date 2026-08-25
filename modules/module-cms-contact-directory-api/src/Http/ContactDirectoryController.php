<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContactDirectory\Services\ContactDirectoryService;

final class ContactDirectoryController
{
    public function index(Request $request, ContactDirectoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->contacts($request->user()?->current_team_id, $request->boolean('public_only', true), $request->integer('page_size', 25))]);
    }

    public function store(Request $request, ContactDirectoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->saveContact($request->validate(['name' => ['required', 'string', 'max:160'], 'email' => ['nullable', 'email'], 'department' => ['nullable', 'string'], 'phone' => ['nullable', 'string'], 'details' => ['array'], 'is_public' => ['boolean']]), $request->user()?->current_team_id)], 201);
    }

    public function category(Request $request, ContactDirectoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->category($request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string']]), $request->user()?->current_team_id)], 201);
    }

    public function location(Request $request, ContactDirectoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->location($request->validate(['name' => ['required', 'string', 'max:160'], 'address' => ['nullable', 'string'], 'city' => ['nullable', 'string'], 'country' => ['nullable', 'string']]), $request->user()?->current_team_id)], 201);
    }

    public function form(Request $request, ContactDirectoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->form($request->validate(['name' => ['required', 'string', 'max:120'], 'schema' => ['required', 'array'], 'is_active' => ['boolean']]), $request->user()?->current_team_id)], 201);
    }
}

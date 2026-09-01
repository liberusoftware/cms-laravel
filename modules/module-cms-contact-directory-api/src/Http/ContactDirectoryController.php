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
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'email' => ['nullable', 'email'], 'department' => ['nullable', 'string'], 'phone' => ['nullable', 'string'], 'details' => ['array'], 'is_public' => ['boolean']]);

        return response()->json(['data' => $service->saveContact($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function category(Request $request, ContactDirectoryService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'slug' => ['nullable', 'string']]);

        return response()->json(['data' => $service->category($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function location(Request $request, ContactDirectoryService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'address' => ['nullable', 'string'], 'city' => ['nullable', 'string'], 'country' => ['nullable', 'string']]);

        return response()->json(['data' => $service->location($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function form(Request $request, ContactDirectoryService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'schema' => ['required', 'array'], 'is_active' => ['boolean']]);

        return response()->json(['data' => $service->form($this->normalized($data), $request->user()?->current_team_id)], 201);
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

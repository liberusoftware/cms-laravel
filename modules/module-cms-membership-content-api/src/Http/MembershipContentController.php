<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\MembershipContent\Models\MembershipContent;
use Liberu\Cms\MembershipContent\Services\MembershipContentService;

final class MembershipContentController
{
    public function index(Request $request, MembershipContentService $service): JsonResponse
    {
        return response()->json(['data' => $service->list($request->user()?->current_team_id, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, MembershipContentService $service): JsonResponse
    {
        return response()->json(['data' => $service->create($this->validated($request, [
            'title' => ['required', 'string', 'max:200'],
            'subject_type' => ['required', 'string', 'max:120'],
            'subject_key' => ['required', 'string', 'max:180'],
            'status' => ['sometimes', 'in:draft,published,archived'],
            'description' => ['nullable', 'string'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date'],
        ]), $request->user()?->current_team_id)], 201);
    }

    public function update(string $content, Request $request, MembershipContentService $service): JsonResponse
    {
        $model = MembershipContent::query()->where('team_id', $request->user()?->current_team_id)->where('public_id', $content)->firstOrFail();

        return response()->json(['data' => $service->update($model, $this->validated($request, ['title' => ['sometimes', 'string', 'max:200'], 'status' => ['sometimes', 'in:draft,published,archived'], 'description' => ['nullable', 'string'], 'available_from' => ['nullable', 'date'], 'available_until' => ['nullable', 'date']]))]);
    }

    public function rule(string $content, Request $request, MembershipContentService $service): JsonResponse
    {
        $model = $this->content($content, $request);
        $data = $this->validated($request, ['entitlement_key' => ['required', 'string', 'max:160'], 'minimum_days' => ['nullable', 'integer', 'min:0']]);

        return response()->json(['data' => $service->rule($model, $this->string($data, 'entitlement_key'), $this->integer($data, 'minimum_days'))], 201);
    }

    public function download(string $content, Request $request, MembershipContentService $service): JsonResponse
    {
        $model = $this->content($content, $request);
        $data = $this->validated($request, ['path' => ['required', 'string', 'max:500'], 'filename' => ['required', 'string', 'max:255'], 'mime_type' => ['nullable', 'string', 'max:160'], 'size' => ['nullable', 'integer', 'min:0'], 'checksum' => ['nullable', 'string', 'max:128']]);

        return response()->json(['data' => $service->download($model, $data)], 201);
    }

    public function grant(Request $request, MembershipContentService $service): JsonResponse
    {
        $data = $this->validated($request, ['subject_type' => ['required', 'string', 'max:120'], 'subject_key' => ['required', 'string', 'max:180'], 'entitlement_key' => ['required', 'string', 'max:160'], 'starts_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date'], 'external_id' => ['nullable', 'string', 'max:180']]);

        return response()->json(['data' => $service->grantEntitlement($this->string($data, 'subject_type'), $this->string($data, 'subject_key'), $this->string($data, 'entitlement_key'), $request->user()?->current_team_id, $this->nullableString($data, 'starts_at'), $this->nullableString($data, 'expires_at'), $this->nullableString($data, 'external_id'))], 201);
    }

    public function revoke(Request $request, MembershipContentService $service): JsonResponse
    {
        $data = $this->validated($request, ['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'entitlement_key' => ['required', 'string']]);

        return response()->json(['revoked' => $service->revokeEntitlement($this->string($data, 'subject_type'), $this->string($data, 'subject_key'), $this->string($data, 'entitlement_key'), $request->user()?->current_team_id)]);
    }

    private function content(string $publicId, Request $request): MembershipContent
    {
        return MembershipContent::query()->where('team_id', $request->user()?->current_team_id)->where('public_id', $publicId)->firstOrFail();
    }

    /**
     * @param  array<string, array<int, mixed>>  $rules
     * @return array<string, mixed>
     */
    private function validated(Request $request, array $rules): array
    {
        $data = $request->validate($rules);
        if (! is_array($data)) {
            return [];
        }

        $validated = [];
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $validated[$key] = $value;
            }
        }

        return $validated;
    }

    /** @param array<string, mixed> $data */
    private function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        abort_unless(is_string($value), 422);

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function nullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        abort_unless($value === null || is_string($value), 422);

        return $value;
    }

    /** @param array<string, mixed> $data */
    private function integer(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;
        abort_unless($value === null || is_int($value), 422);

        return $value;
    }
}

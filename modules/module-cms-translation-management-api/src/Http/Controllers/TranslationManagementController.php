<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\TranslationManagement\Actions\TranslationManagementService;
use Liberu\Cms\TranslationManagement\Models\TranslationSourceChange;
use Liberu\Cms\TranslationManagement\Queries\TranslationJobQuery;
use Liberu\Cms\TranslationManagementApi\Http\Resources\TranslationGlossaryResource;
use Liberu\Cms\TranslationManagementApi\Http\Resources\TranslationJobResource;
use Liberu\Cms\TranslationManagementApi\Http\Resources\TranslationMemoryResource;
use Liberu\Cms\TranslationManagementApi\Http\Resources\TranslationSourceChangeResource;
use Liberu\Cms\TranslationManagementApi\Http\Resources\TranslationVendorResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TranslationManagementController
{
    public function __construct(private readonly TranslationJobQuery $jobs, private readonly TranslationManagementService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return TranslationJobResource::collection($this->jobs->paginate($request->integer('per_page', 15), (string) $request->string('search'), $request->string('status')->toString() ?: null));
    }

    public function show(string $publicId): TranslationJobResource
    {
        $job = $this->jobs->find($publicId);
        if (! $job) {
            throw new NotFoundHttpException;
        }

        return new TranslationJobResource($job);
    }

    public function create(Request $request): TranslationJobResource
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'source_locale' => ['required', 'string', 'max:16'], 'target_locale' => ['required', 'string', 'max:16'], 'external_key' => ['nullable', 'string', 'max:255'], 'vendor_key' => ['nullable', 'string', 'max:255'], 'metadata' => ['nullable', 'array']]);

        return new TranslationJobResource($this->service->createJob($data));
    }

    public function sourceChange(Request $request, string $publicId): TranslationSourceChangeResource
    {
        $job = $this->jobs->find($publicId);
        if (! $job) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['subject_type' => ['required', 'string', 'max:255'], 'subject_id' => ['required', 'string', 'max:255'], 'field' => ['required', 'string', 'max:255'], 'source_text' => ['required', 'string'], 'source_version' => ['nullable', 'string', 'max:255']]);

        return new TranslationSourceChangeResource($this->service->addSourceChange($job, $data));
    }

    public function translate(Request $request, int $sourceChange): TranslationSourceChangeResource
    {
        $change = TranslationSourceChange::query()->find($sourceChange);
        if (! $change) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['vendor_key' => ['required', 'string', 'max:255'], 'context' => ['nullable', 'array']]);

        return new TranslationSourceChangeResource($this->service->translate($change, $data['vendor_key'], $data['context'] ?? []));
    }

    public function review(Request $request, int $sourceChange): TranslationSourceChangeResource
    {
        $change = TranslationSourceChange::query()->find($sourceChange);
        if (! $change) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['decision' => ['required', 'in:approved,rejected'], 'notes' => ['nullable', 'string', 'max:2000']]);

        return new TranslationSourceChangeResource($this->service->review($change, $data['decision'], $data['notes'] ?? null));
    }

    public function reconcile(string $publicId): TranslationJobResource
    {
        $job = $this->jobs->find($publicId);
        if (! $job) {
            throw new NotFoundHttpException;
        }

        return new TranslationJobResource($this->service->reconcile($job));
    }

    public function queue(string $publicId): TranslationJobResource
    {
        $job = $this->jobs->find($publicId);
        if (! $job) {
            throw new NotFoundHttpException;
        }

        return new TranslationJobResource($this->service->queue($job));
    }

    public function assign(Request $request, int $sourceChange): mixed
    {
        $change = TranslationSourceChange::query()->find($sourceChange);
        if (! $change) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['assignee_type' => ['required', 'string', 'max:255'], 'assignee_id' => ['required', 'string', 'max:255'], 'role' => ['required', 'string', 'max:64'], 'due_at' => ['nullable', 'date']]);

        return response()->json(['data' => $this->service->assign($change, $data['assignee_type'], $data['assignee_id'], $data['role'], $data['due_at'] ?? null)], 201);
    }

    public function vendor(Request $request): TranslationVendorResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'driver' => ['required', 'string', 'max:255'], 'name' => ['nullable', 'string', 'max:255'], 'status' => ['nullable', 'string', 'max:32'], 'settings' => ['nullable', 'array']]);

        return new TranslationVendorResource($this->service->addVendor($data));
    }

    public function memory(Request $request): TranslationMemoryResource
    {
        $data = $request->validate(['source_locale' => ['required', 'string', 'max:16'], 'target_locale' => ['required', 'string', 'max:16'], 'source_text' => ['required', 'string'], 'translated_text' => ['required', 'string'], 'metadata' => ['nullable', 'array']]);

        return new TranslationMemoryResource($this->service->remember($data['source_locale'], $data['target_locale'], $data['source_text'], $data['translated_text'], $data['metadata'] ?? []));
    }

    public function glossary(Request $request): TranslationGlossaryResource
    {
        $data = $request->validate(['source_locale' => ['required', 'string', 'max:16'], 'target_locale' => ['required', 'string', 'max:16'], 'source_term' => ['required', 'string', 'max:255'], 'preferred_term' => ['required', 'string', 'max:255'], 'forbidden_terms' => ['nullable', 'array']]);

        return new TranslationGlossaryResource($this->service->glossary($data));
    }
}

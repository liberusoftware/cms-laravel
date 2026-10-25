<?php

namespace Liberu\Cms\EmbedsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Embeds\Queries\EmbedsQuery;
use Liberu\Cms\Embeds\Services\EmbedsService;
use Liberu\Cms\EmbedsApi\Http\Resources\EmbedResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EmbedsController
{
    public function index(Request $r, EmbedsQuery $q): JsonResponse
    {
        $d = $r->validate(['search' => ['nullable', 'string'], 'per_page' => ['integer', 'min:1', 'max:100']]);
        $p = $q->list((int) ($d['per_page'] ?? 15), (string) ($d['search'] ?? ''));

        return response()->json(['data' => EmbedResource::collection($p->getCollection()), 'meta' => ['current_page' => $p->currentPage(), 'last_page' => $p->lastPage(), 'total' => $p->total()]]);
    }

    public function show(int $id, EmbedsQuery $q): EmbedResource
    {
        $e = $q->find($id);
        if (! $e || $e->status !== 'published') {
            throw new NotFoundHttpException;
        }

        return new EmbedResource($e);
    }

    public function store(Request $r, EmbedsService $s): EmbedResource
    {
        $d = $r->validate(['provider_id' => ['required', 'integer'], 'external_key' => ['required', 'string', 'max:255'], 'url' => ['required', 'url'], 'title' => ['nullable', 'string'], 'privacy_mode' => ['in:public,private,consent'], 'fallback_url' => ['nullable', 'url'], 'aspect_ratio' => ['nullable', 'regex:/^\\d+:\\d+$/'], 'responsive' => ['boolean'], 'metadata' => ['array']]);

        return new EmbedResource($s->embed($d, $r->user()?->current_team_id));
    }

    public function render(int $id, Request $r, EmbedsQuery $q, EmbedsService $s): JsonResponse
    {
        $e = $q->find($id);
        if (! $e) {
            throw new NotFoundHttpException;
        }

        return response()->json(['data' => $s->render($e, (bool) $r->boolean('consented'))]);
    }
}

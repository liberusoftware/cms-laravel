<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Taxonomy\Models\Taxonomy;
use Liberu\Cms\Taxonomy\Services\TaxonomyService;

final class TaxonomyController
{
    public function index(Request $request, TaxonomyService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['sometimes', 'string'], 'search' => ['sometimes', 'nullable', 'string']]);
        $query = Taxonomy::query()->when($data['key'] ?? null, fn ($q, string $key) => $q->where('key', $key));
        $items = $query->get()->map(fn (Taxonomy $taxonomy): array => ['id' => $taxonomy->id, 'key' => $taxonomy->key, 'name' => $taxonomy->name, 'hierarchical' => $taxonomy->hierarchical, 'terms' => array_map(fn ($term): array => ['id' => $term->id, 'slug' => $term->slug, 'name' => $term->name, 'parent_id' => $term->parent_id, 'assignments_count' => $term->assignments_count ?? 0], $service->terms($taxonomy, $data['search'] ?? null))]);

        return response()->json(['data' => $items]);
    }
}

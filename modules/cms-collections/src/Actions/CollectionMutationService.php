<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Models\CollectionItem;

final class CollectionMutationService
{
    public function create(array $attributes): Collection
    {
        if (blank($attributes['name'] ?? null)) throw ValidationException::withMessages(['name' => 'Collection name is required.']);
        return DB::transaction(fn (): Collection => Collection::create($this->only($attributes, ['name', 'slug', 'type', 'description', 'schema', 'team_id'])));
    }

    public function update(Collection $collection, array $attributes): Collection
    {
        $collection->fill($this->only($attributes, ['name', 'slug', 'type', 'description', 'schema']))->save();
        return $collection->refresh();
    }

    public function delete(Collection $collection): void { DB::transaction(fn () => $collection->delete()); }

    public function createItem(Collection $collection, array $attributes): CollectionItem
    {
        if (blank($attributes['title'] ?? null)) throw ValidationException::withMessages(['title' => 'Collection item title is required.']);
        return DB::transaction(fn (): CollectionItem => $collection->items()->create($this->only($attributes, ['title', 'slug', 'content', 'excerpt', 'data', 'metadata', 'status', 'published_at', 'user_id', 'team_id'])));
    }

    public function updateItem(CollectionItem $item, array $attributes): CollectionItem
    {
        $item->fill($this->only($attributes, ['title', 'slug', 'content', 'excerpt', 'data', 'metadata', 'status', 'published_at']))->save();
        return $item->refresh();
    }

    public function deleteItem(CollectionItem $item): void { DB::transaction(fn () => $item->delete()); }

    private function only(array $attributes, array $allowed): array { return array_intersect_key($attributes, array_flip($allowed)); }
}

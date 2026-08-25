<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollections\Actions;

use Liberu\Cms\Collections\Actions\CollectionMutationService;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Models\CollectionItem;

/** Canonical mutation boundary; invariants remain in the domain service. */
final class StructuredCollectionMutationService
{
    public function __construct(private readonly CollectionMutationService $legacy) {}

    public function create(array $attributes): Collection { return $this->legacy->create($attributes); }
    public function update(Collection $collection, array $attributes): Collection { return $this->legacy->update($collection, $attributes); }
    public function delete(Collection $collection): void { $this->legacy->delete($collection); }
    public function createRecord(Collection $collection, array $attributes): CollectionItem { return $this->legacy->createItem($collection, $attributes); }
    public function updateRecord(CollectionItem $record, array $attributes): CollectionItem { return $this->legacy->updateItem($record, $attributes); }
    public function deleteRecord(CollectionItem $record): void { $this->legacy->deleteItem($record); }
}

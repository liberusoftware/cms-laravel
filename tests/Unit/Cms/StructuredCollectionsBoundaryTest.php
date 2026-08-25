<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Collections\Actions\CollectionMutationService;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Queries\CollectionQuery;

uses(RefreshDatabase::class);

it('owns collection and item mutations behind the domain service', function (): void {
    $service = app(CollectionMutationService::class);
    $collection = $service->create(['name' => 'FAQs', 'type' => 'faq', 'schema' => ['question' => 'text']]);
    $item = $service->createItem($collection, ['title' => 'How?', 'content' => 'Like this.', 'status' => 'published', 'published_at' => now()]);
    $service->updateItem($item, ['title' => 'How does it work?']);

    expect($collection->slug)->toBe('faqs')
        ->and($item->fresh()->title)->toBe('How does it work?')
        ->and(app(CollectionQuery::class)->published('faqs')->total())->toBe(1);

    $service->deleteItem($item->fresh());
    $service->delete($collection->fresh());
    expect(Collection::query()->count())->toBe(0);
});

it('rejects invalid collection records at the mutation boundary', function (): void {
    expect(fn () => app(CollectionMutationService::class)->create(['type' => 'faq']))->toThrow(ValidationException::class);
});

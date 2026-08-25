<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Actions\ContentEntryMutationService;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;

uses(RefreshDatabase::class);

it('clones a content entity as draft and preserves relationships', function (): void {
    $type = ContentType::factory()->create(['key' => 'article']);
    $source = ContentEntry::factory()->create([
        'content_type_id' => $type->id,
        'title' => 'Source',
        'status' => 'published',
        'published_at' => now()->subMinute(),
    ]);
    $related = ContentEntry::factory()->create(['content_type_id' => $type->id, 'title' => 'Related']);
    $source->relateTo($related, 'recommended', 2);

    $clone = app(ContentEntryMutationService::class)->clone($source, 'Copied');

    expect($clone->title)->toBe('Copied')
        ->and($clone->status->value)->toBe('draft')
        ->and($clone->published_at)->toBeNull()
        ->and($clone->canonical_id)->not->toBe($source->canonical_id)
        ->and($clone->relatedEntries)->toHaveCount(1)
        ->and($clone->relatedEntries->first()->pivot->relation)->toBe('recommended')
        ->and($clone->relatedEntries->first()->pivot->position)->toBe(2);
});

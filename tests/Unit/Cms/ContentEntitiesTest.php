<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentEntitiesLivewire\Livewire\EntityBrowser;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function entityType(): ContentType
{
    return ContentType::create([
        'key' => 'article',
        'name' => 'Article',
        'singular_label' => 'Article',
        'plural_label' => 'Articles',
        'fields' => [['name' => 'body', 'label' => 'Body', 'type' => 'textarea']],
    ]);
}

it('assigns a canonical identifier and stores authorship', function (): void {
    $entry = ContentEntry::create([
        'content_type_id' => entityType()->id,
        'title' => 'A guide',
        'author_id' => 99,
        'data' => ['body' => 'Text'],
    ]);

    expect($entry->canonical_id)->toBe('article:a-guide')
        ->and($entry->authorId())->toBe(99);
});

it('clones an entity as a new draft with a new canonical identifier', function (): void {
    $entry = ContentEntry::create([
        'content_type_id' => entityType()->id,
        'title' => 'Original',
        'status' => 'published',
        'published_at' => now(),
        'data' => ['body' => 'Copied'],
    ]);

    $clone = $entry->cloneEntity('Copy');

    expect($clone->exists)->toBeTrue()
        ->and($clone->id)->not->toBe($entry->id)
        ->and($clone->status->value)->toBe('draft')
        ->and($clone->canonical_id)->toBe('article:copy')
        ->and($clone->data)->toBe(['body' => 'Copied']);
});

it('maintains typed relationships between entities', function (): void {
    $type = entityType();
    $source = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'Source']);
    $target = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'Target']);

    $source->relateTo($target, 'references', 2);

    expect($source->fresh()->relatedEntries->first()->is($target))->toBeTrue()
        ->and($source->fresh()->relatedEntries->first()->pivot->relation)->toBe('references')
        ->and($source->fresh()->relatedEntries->first()->pivot->position)->toBe(2);
});

it('rejects relationships that cross tenant boundaries', function (): void {
    $type = entityType();
    $source = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'Source', 'team_id' => 3]);
    $target = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'Target', 'team_id' => 4]);

    expect(fn () => $source->relateTo($target))->toThrow(ValidationException::class);
});

it('browses published entities through the Livewire presentation adapter', function (): void {
    $type = entityType();
    $visible = ContentEntry::create([
        'content_type_id' => $type->id,
        'title' => 'Published entity',
        'status' => 'published',
        'published_at' => now(),
    ]);
    ContentEntry::create(['content_type_id' => $type->id, 'title' => 'Draft entity']);

    Livewire::test(EntityBrowser::class, ['type' => $type->key])
        ->assertSee($visible->title)
        ->assertDontSee('Draft entity')
        ->set('search', str_repeat('x', 400))
        ->assertSet('search', str_repeat('x', 255));
});

it('resolves one published entity by type and slug', function (): void {
    $type = entityType();
    $visible = ContentEntry::create([
        'content_type_id' => $type->id,
        'title' => 'A guide',
        'slug' => 'a-guide',
        'status' => 'published',
        'published_at' => now()->subMinute(),
    ]);
    ContentEntry::create([
        'content_type_id' => $type->id,
        'title' => 'Draft guide',
        'slug' => 'draft-guide',
    ]);

    expect(app(PublishedEntityQuery::class)->find('article', 'a-guide')?->is($visible))->toBeTrue()
        ->and(app(PublishedEntityQuery::class)->find('article', 'draft-guide'))->toBeNull();
});

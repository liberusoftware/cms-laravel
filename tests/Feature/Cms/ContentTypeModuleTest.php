<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\ContentTypes\Schema\InvalidContentData;
use Liberu\Cms\ContentTypes\Schema\SchemaValidator;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;

uses(RefreshDatabase::class);

function portfolioType(): ContentType
{
    return ContentType::create([
        'key' => 'portfolio',
        'name' => 'Portfolio',
        'singular_label' => 'Item',
        'plural_label' => 'Items',
        'fields' => [
            ['name' => 'summary', 'label' => 'Summary', 'type' => 'text', 'required' => true],
            ['name' => 'year', 'label' => 'Year', 'type' => 'number', 'required' => false],
        ],
    ]);
}

it('exposes its schema as field definitions', function (): void {
    $definitions = portfolioType()->fieldDefinitions();

    expect($definitions)->toHaveCount(2)
        ->and($definitions[0]->name)->toBe('summary')
        ->and($definitions[0]->type)->toBe('text')
        ->and($definitions[0]->required)->toBeTrue();
});

it('validates entry data against the schema and drops unknown fields', function (): void {
    $type = portfolioType();

    $data = app(SchemaValidator::class)->validate($type, [
        'summary' => 'A neat project',
        'year' => 2026,
        'rogue' => 'ignored',
    ]);

    expect($data)->toBe(['summary' => 'A neat project', 'year' => 2026]);
});

it('rejects data missing a required field', function (): void {
    $type = portfolioType();

    expect(fn () => app(SchemaValidator::class)->validate($type, ['year' => 2026]))
        ->toThrow(InvalidContentData::class);
});

it('rejects a value of the wrong type', function (): void {
    $type = portfolioType();

    expect(fn () => app(SchemaValidator::class)->validate($type, ['summary' => 'ok', 'year' => 'not-a-number']))
        ->toThrow(InvalidContentData::class);
});

it('creates a workflow-enabled entry belonging to its type', function (): void {
    $type = portfolioType();
    $entry = ContentEntry::create([
        'content_type_id' => $type->id,
        'title' => 'My Project',
        'data' => ['summary' => 'A neat project'],
    ]);

    expect($entry->slug)->toBe('my-project')
        ->and($entry->type->is($type))->toBeTrue()
        ->and($entry->contentType())->toBe('portfolio');
});

it('publishes an entry and reports its type key in the event', function (): void {
    Event::fake([ContentPublished::class]);
    $type = portfolioType();
    $entry = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'X', 'data' => []]);

    $entry->publish();

    Event::assertDispatched(ContentPublished::class, fn (ContentPublished $e): bool => $e->contentType === 'portfolio');
});

it('versions and reverts entry data', function (): void {
    $type = portfolioType();
    $entry = ContentEntry::create(['content_type_id' => $type->id, 'title' => 'X', 'data' => ['summary' => 'one']]);
    $entry->recordRevision();
    $entry->update(['data' => ['summary' => 'two']]);

    $entry->revertTo(1);

    expect($entry->fresh()->data)->toBe(['summary' => 'one']);
});

it('queries entries by type and publication through the repository', function (): void {
    $type = portfolioType();
    ContentEntry::factory()->published()->for($type, 'type')->create();
    ContentEntry::factory()->for($type, 'type')->create();

    $repository = app(ContentEntryRepositoryInterface::class);

    expect($repository->ofType('portfolio'))->toHaveCount(2)
        ->and($repository->published())->toHaveCount(1);
});

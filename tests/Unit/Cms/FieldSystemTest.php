<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\ContentTypes\Schema\InvalidContentData;
use Liberu\Cms\ContentTypes\Schema\SchemaValidator;
use Liberu\Cms\FieldSystemLivewire\Livewire\SchemaBrowser;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('applies defaults and validates cardinality, conditional, and computed fields', function (): void {
    $type = ContentType::create([
        'key' => 'product',
        'name' => 'Product',
        'singular_label' => 'Product',
        'plural_label' => 'Products',
        'fields' => [
            ['name' => 'status', 'label' => 'Status', 'type' => 'text', 'default' => 'draft'],
            ['name' => 'tags', 'label' => 'Tags', 'type' => 'text', 'cardinality' => 'many', 'validation' => ['minItems' => 1]],
            ['name' => 'sku', 'label' => 'SKU', 'type' => 'text', 'condition' => ['field' => 'status', 'equals' => 'published']],
            ['name' => 'search_text', 'label' => 'Search', 'type' => 'text', 'computed' => true],
        ],
    ]);

    expect(app(SchemaValidator::class)->validate($type, ['tags' => ['one', 'two']]))
        ->toBe(['status' => 'draft', 'tags' => ['one', 'two']]);

    expect(app(SchemaValidator::class)->validate($type, ['status' => 'published', 'tags' => ['one'], 'sku' => 'P-1']))
        ->toBe(['status' => 'published', 'tags' => ['one'], 'sku' => 'P-1']);

    expect(fn () => app(SchemaValidator::class)->validate($type, ['tags' => []]))
        ->toThrow(InvalidContentData::class);
});

it('retains previous schemas when a content type is migrated', function (): void {
    $type = ContentType::create([
        'key' => 'faq',
        'name' => 'FAQ',
        'singular_label' => 'FAQ',
        'plural_label' => 'FAQs',
        'fields' => [['name' => 'question', 'label' => 'Question', 'type' => 'text']],
    ]);

    $type->migrateSchema([
        ['name' => 'question', 'label' => 'Question', 'type' => 'text', 'required' => true],
        ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea'],
    ], 'add answer');

    expect($type->fresh()->schema_version)->toBe(2)
        ->and($type->fresh()->schema_history)->toHaveCount(1)
        ->and($type->fresh()->schema_history[0]['reason'])->toBe('add answer');
});

it('renders the field schema through the Livewire adapter', function (): void {
    ContentType::create([
        'key' => 'profile',
        'name' => 'Profile',
        'singular_label' => 'Profile',
        'plural_label' => 'Profiles',
        'fields' => [['name' => 'bio', 'label' => 'Biography', 'type' => 'textarea']],
    ]);

    Livewire::test(SchemaBrowser::class, ['type' => 'profile'])
        ->assertSee('Biography')
        ->assertSee('textarea');
});

it('fails closed for malformed conditional field definitions', function (): void {
    $type = ContentType::create([
        'key' => 'conditional',
        'name' => 'Conditional',
        'singular_label' => 'Conditional',
        'plural_label' => 'Conditionals',
        'fields' => [['name' => 'secret', 'label' => 'Secret', 'type' => 'text', 'condition' => ['field' => 'status']]],
    ]);

    expect(app(SchemaValidator::class)->validate($type, ['status' => 'published', 'secret' => 'value']))->toBe([]);
});

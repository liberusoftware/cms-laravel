<?php

declare(strict_types=1);

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\ContentTypes\Schema\InvalidContentData;
use Liberu\Cms\ContentTypes\Schema\SchemaValidator;
use Liberu\Cms\Contracts\Fields\FieldTypeDefinition;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

uses(RefreshDatabase::class);

function registerColorKind(): void
{
    app(FieldTypeRegistryInterface::class)->register(new FieldTypeDefinition(
        'color',
        'Color',
        static fn (string $path, array $options): TextInput => TextInput::make($path),
        static fn (mixed $value): bool => is_string($value) && str_starts_with($value, '#'),
    ));
}

function swatchType(): ContentType
{
    return ContentType::create([
        'key' => 'swatch',
        'name' => 'Swatch',
        'singular_label' => 'Swatch',
        'plural_label' => 'Swatches',
        'fields' => [['name' => 'shade', 'label' => 'Shade', 'type' => 'color', 'required' => true]],
    ]);
}

it('seeds the built-in field kinds', function (): void {
    expect(app(FieldTypeRegistryInterface::class)->options())
        ->toHaveKeys(['text', 'textarea', 'richtext', 'number', 'boolean', 'date', 'select', 'media']);
});

it('lets an extension register a custom field kind that appears in the schema editor', function (): void {
    registerColorKind();

    $registry = app(FieldTypeRegistryInterface::class);

    expect($registry->has('color'))->toBeTrue()
        ->and($registry->options())->toHaveKey('color', 'Color');
});

it('validates a custom field kind through its registered predicate', function (): void {
    registerColorKind();
    $type = swatchType();
    $validator = app(SchemaValidator::class);

    expect($validator->validate($type, ['shade' => '#fff']))->toBe(['shade' => '#fff']);

    expect(fn () => $validator->validate($type, ['shade' => 'not-a-color']))
        ->toThrow(InvalidContentData::class);
});

it('rejects a value whose field kind is not registered', function (): void {
    $type = ContentType::create([
        'key' => 'weird',
        'name' => 'Weird',
        'singular_label' => 'Weird',
        'plural_label' => 'Weirds',
        'fields' => [['name' => 'thing', 'label' => 'Thing', 'type' => 'unregistered', 'required' => false]],
    ]);

    expect(fn () => app(SchemaValidator::class)->validate($type, ['thing' => 'x']))
        ->toThrow(InvalidContentData::class);
});

it('builds a Filament field component for a kind through the registry', function (): void {
    registerColorKind();

    $definition = app(FieldTypeRegistryInterface::class)->get('color');
    $this->assertInstanceOf(FieldTypeDefinition::class, $definition);

    $component = ($definition->component)('data.shade', []);
    $this->assertInstanceOf(Field::class, $component);

    expect($component->getName())->toBe('data.shade');
});

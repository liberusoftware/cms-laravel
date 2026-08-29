<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FieldSystem\Services\FieldSystemService;

uses(RefreshDatabase::class);

it('versions reusable schemas and applies defaults, conditions, and cardinality', function (): void {
    $service = app(FieldSystemService::class);
    $fields = [['name' => 'title', 'type' => 'text', 'required' => true], ['name' => 'tags', 'type' => 'text', 'cardinality' => 'many'], ['name' => 'summary', 'type' => 'text', 'default' => 'Default', 'condition' => ['field' => 'title', 'equals' => 'Hello']]];
    $schema = $service->saveSchema(3, 'article', 'Article', $fields);
    $schema = $service->saveSchema(3, 'article', 'Article', $fields, 'Add reusable field rules');
    expect($schema->version)->toBe(2)->and(count($schema->history))->toBe(1)->and($service->validateData($schema, ['title' => 'Hello', 'tags' => ['one', 'two']]))->toMatchArray(['summary' => 'Default']);
});

it('rejects duplicate, unknown, and invalid field definitions', function (): void {
    $service = app(FieldSystemService::class);
    $field = ['name' => 'title', 'type' => 'text'];
    expect(fn () => $service->saveSchema(3, 'article', 'Article', [$field, $field]))->toThrow(ValidationException::class)
        ->and(fn () => $service->saveSchema(3, 'article', 'Article', [['name' => 'x', 'type' => 'missing']]))->toThrow(ValidationException::class);
});

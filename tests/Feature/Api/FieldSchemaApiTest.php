<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\ContentTypes\Models\ContentType;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
});

it('returns the complete versioned field schema through the delivery API', function (): void {
    ContentType::factory()->create([
        'key' => 'product',
        'fields' => [[
            'name' => 'tags',
            'label' => 'Tags',
            'type' => 'text',
            'cardinality' => 'many',
            'default' => [],
            'validation' => ['minItems' => 1],
            'group' => 'catalogue',
        ]],
    ]);

    $this->getJson('/api/v1/field-schemas/product')
        ->assertOk()
        ->assertJsonPath('data.key', 'product')
        ->assertJsonPath('data.version', 1)
        ->assertJsonPath('data.fields.0.cardinality', 'many')
        ->assertJsonPath('data.fields.0.validation.minItems', 1)
        ->assertJsonPath('data.fields.0.group', 'catalogue');
});

it('does not expose unknown or unsafe schema keys', function (): void {
    $this->getJson('/api/v1/field-schemas/unknown')->assertNotFound();
    $this->getJson('/api/v1/field-schemas/%2Fetc%2Fpasswd')->assertNotFound();
});

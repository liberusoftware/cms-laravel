<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

it('serves a structurally valid OpenAPI 3 document without a token', function (): void {
    $spec = $this->getJson('/api/v1/openapi.json')->assertOk()->json();

    expect($spec['openapi'])->toStartWith('3.')
        ->and($spec['info']['title'])->not->toBeEmpty()
        ->and($spec['info']['version'])->not->toBeEmpty()
        ->and($spec['paths'])->not->toBeEmpty()
        ->and($spec['components']['securitySchemes']['sanctum']['scheme'])->toBe('bearer');

    foreach ($spec['paths'] as $operations) {
        foreach ($operations as $operation) {
            expect($operation['responses'])->not->toBeEmpty()
                ->and($operation)->toHaveKeys(['operationId', 'summary', 'tags', 'security']);
        }
    }
});

it('documents every registered /api/v1 route (drift guard)', function (): void {
    $spec = $this->getJson('/api/v1/openapi.json')->assertOk()->json();

    foreach (Route::getRoutes() as $route) {
        $uri = $route->uri();

        if (! str_starts_with($uri, 'api/v1')) {
            continue;
        }

        $path = '/'.str_replace('?}', '}', $uri);

        foreach ($route->methods() as $method) {
            if (in_array($method, ['HEAD', 'OPTIONS'], true)) {
                continue;
            }

            expect($spec['paths'][$path][strtolower($method)] ?? null)
                ->not->toBeNull("Route [{$method} {$uri}] is missing from the OpenAPI spec.");
        }
    }
});

it('marks write endpoints with the token security and full error shapes', function (): void {
    $spec = $this->getJson('/api/v1/openapi.json')->assertOk()->json();

    $store = $spec['paths']['/api/v1/pages']['post'];

    expect($store['security'])->toBe([['sanctum' => []]])
        ->and($store['description'])->toContain('content:write')
        ->and($store['responses'])->toHaveKeys(['201', '401', '403', '422', '429']);
});

it('marks the preview endpoint as public and signed', function (): void {
    $spec = $this->getJson('/api/v1/openapi.json')->assertOk()->json();

    $preview = $spec['paths']['/api/v1/preview/{type}/{id}']['get'];

    expect($preview['security'])->toBe([])
        ->and($preview['responses'])->toHaveKeys(['200', '403', '404']);
});

it('documents pagination and taxonomy query parameters', function (): void {
    $spec = $this->getJson('/api/v1/openapi.json')->assertOk()->json();

    $names = array_column($spec['paths']['/api/v1/posts']['get']['parameters'], 'name');

    expect($names)->toContain('page')->toContain('per_page')->toContain('category')->toContain('tag');
});

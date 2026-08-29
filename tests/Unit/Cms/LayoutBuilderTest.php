<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\LayoutBuilder\Services\LayoutBuilderService;

uses(RefreshDatabase::class);

it('validates, publishes, and resolves tenant-scoped layouts', function (): void {
    $service = app(LayoutBuilderService::class);
    $layout = $service->create(['name' => 'Article layout', 'target_type' => 'content_entry', 'target_id' => '7', 'definition' => ['regions' => ['main' => [['component' => 'text', 'settings' => ['text' => 'Hello']]]]], 'team_id' => 10]);
    $service->publish($layout);

    expect($service->resolve('content_entry', 7)?->is($layout))->toBeTrue();
});

it('rejects unknown layout regions and components', function (): void {
    expect(fn () => app(LayoutBuilderService::class)->create(['name' => 'Unsafe', 'target_type' => 'page', 'target_id' => '1', 'definition' => ['regions' => ['unknown' => [['component' => 'script', 'settings' => []]]]]]))->toThrow(ValidationException::class);
});

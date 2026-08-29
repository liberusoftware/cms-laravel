<?php

declare(strict_types=1);

use Liberu\Cms\ExtensionManager\Services\ExtensionManagerService;

it('exposes registered extension descriptors and lifecycle state', function (): void {
    $extensions = app(ExtensionManagerService::class)->all();
    $core = collect($extensions)->firstWhere('foundational', true);

    expect($core)->toBeArray()->and($core['enabled'])->toBeTrue();
});

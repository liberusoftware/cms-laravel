<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ThemeMarketplace\Queries\ThemeMarketplaceQuery;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;

uses(RefreshDatabase::class);

it('keeps pending themes out of the public catalog and enforces installation updates', function (): void {
    $service = app(ThemeMarketplaceService::class);
    $query = app(ThemeMarketplaceQuery::class);
    $v1 = $service->publish(['key' => 'clean', 'name' => 'Clean', 'version' => '1.0.0', 'author' => 'Liberu', 'compatibility' => ['cms' => '1.0.0']]);
    expect($query->catalog()->total())->toBe(0);
    $service->reviewSecurity($v1, 'approved');
    expect($query->catalog()->total())->toBe(1);

    $installation = $service->install($v1, 'docs', '1.0.0');
    $v2 = $service->publish(['key' => 'clean', 'name' => 'Clean', 'version' => '1.1.0', 'author' => 'Liberu']);
    $service->reviewSecurity($v2, 'approved');
    $updated = $service->update($installation, $v2, '1.0.0');

    expect($updated->installed_version)->toBe('1.1.0')
        ->and(fn () => $service->update($updated, $v2, '1.0.0'))->toThrow(ValidationException::class);
});

it('validates theme manifest versions and preview URLs', function (): void {
    expect(fn () => app(ThemeMarketplaceService::class)->publish(['key' => 'bad', 'name' => 'Bad', 'version' => '1', 'author' => 'Test']))->toThrow(ValidationException::class)
        ->and(fn () => app(ThemeMarketplaceService::class)->publish(['key' => 'bad', 'name' => 'Bad', 'version' => '1.0.0', 'author' => 'Test', 'preview_url' => 'not-url']))->toThrow(ValidationException::class);
});

<?php

declare(strict_types=1);

/**
 * Run the package generator against a disposable Laravel base path. The
 * generator intentionally updates the root composer manifest, so the test
 * must not point it at the application checkout.
 */
function withTemporaryModuleBase(callable $callback): void
{
    $originalBase = app()->basePath();
    $temporaryBase = sys_get_temp_dir().'/cms-module-generator-'.bin2hex(random_bytes(8));

    File::makeDirectory("{$temporaryBase}/modules", 0755, true);
    File::put("{$temporaryBase}/composer.json", json_encode([
        'require' => [
            'liberusoftware/module-cms-core' => '^0.1',
        ],
    ], JSON_THROW_ON_ERROR));

    app()->setBasePath($temporaryBase);

    try {
        $callback($temporaryBase);
    } finally {
        app()->setBasePath($originalBase);
        File::deleteDirectory($temporaryBase);
    }
}

it('scaffolds a bounded foundational module and updates the root manifest', function (): void {
    withTemporaryModuleBase(function (string $base): void {
        $this->artisan('cms:make-module', [
            'name' => 'Portfolio',
            '--foundational' => true,
        ])->assertSuccessful();

        $module = "{$base}/modules/cms-portfolio";
        $manifest = json_decode(File::get("{$module}/composer.json"), true, 512, JSON_THROW_ON_ERROR);
        $root = json_decode(File::get("{$base}/composer.json"), true, 512, JSON_THROW_ON_ERROR);

        expect(File::exists("{$module}/src/PortfolioModule.php"))->toBeTrue()
            ->and(File::exists("{$module}/src/PortfolioServiceProvider.php"))->toBeTrue()
            ->and(File::exists("{$module}/database/migrations/.gitkeep"))->toBeTrue()
            ->and($manifest['name'])->toBe('liberusoftware/module-cms-portfolio')
            ->and($manifest['type'])->toBe('liberu-module')
            ->and($manifest['extra']['liberu']['name'])->toBe('module-cms-portfolio')
            ->and($manifest['require']['liberusoftware/module-cms-contracts'])->toBe('^0.1')
            ->and($manifest['require']['liberusoftware/module-cms-core'])->toBe('^0.1')
            ->and($root['require']['liberusoftware/module-cms-portfolio'])->toBe('^0.1');
    });
});

it('refuses to replace an existing module', function (): void {
    withTemporaryModuleBase(function (): void {
        $this->artisan('cms:make-module', ['name' => 'Portfolio'])
            ->assertSuccessful();

        $this->artisan('cms:make-module', ['name' => 'Portfolio'])
            ->assertFailed();
    });
});

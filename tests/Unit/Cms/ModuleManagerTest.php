<?php

declare(strict_types=1);

use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Exceptions\ModuleDependencyException;
use Liberu\Cms\Core\Module\AbstractModule;
use Liberu\Cms\Core\Module\ArrayModuleStateRepository;
use Liberu\Cms\Core\Module\ModuleManager;
use Liberu\Cms\Core\Module\ModuleRegistry;

function fakeModule(string $key, array $dependencies = [], bool $foundational = false): ModuleInterface
{
    return new class($key, $dependencies, $foundational) extends AbstractModule
    {
        public function __construct(
            private readonly string $moduleKey,
            private readonly array $moduleDependencies,
            private readonly bool $moduleFoundational,
        ) {}

        public function key(): string
        {
            return $this->moduleKey;
        }

        public function name(): string
        {
            return ucfirst($this->moduleKey);
        }

        public function version(): string
        {
            return '1.0.0';
        }

        public function dependencies(): array
        {
            return $this->moduleDependencies;
        }

        public function isFoundational(): bool
        {
            return $this->moduleFoundational;
        }
    };
}

/**
 * @param  array<int, ModuleInterface>  $modules
 * @param  array<string, bool>  $state
 */
function makeManager(array $modules, array $state = [], bool $enabledByDefault = true, array $forcedDisabled = []): array
{
    $registry = new ModuleRegistry;

    foreach ($modules as $module) {
        $registry->register($module);
    }

    $manager = new ModuleManager(
        registry: $registry,
        state: new ArrayModuleStateRepository($state),
        enabledByDefault: $enabledByDefault,
        forcedDisabled: $forcedDisabled,
    );

    return [$manager, $registry];
}

it('reports unknown modules as disabled', function (): void {
    [$manager] = makeManager([]);

    expect($manager->isEnabled('ghost'))->toBeFalse();
});

it('always reports foundational modules as enabled regardless of stored state', function (): void {
    [$manager] = makeManager([fakeModule('core', foundational: true)], ['core' => false]);

    expect($manager->isEnabled('core'))->toBeTrue();
});

it('honours the enabled-by-default setting when no state is stored', function (): void {
    [$onByDefault] = makeManager([fakeModule('blog')], enabledByDefault: true);
    [$offByDefault] = makeManager([fakeModule('blog')], enabledByDefault: false);

    expect($onByDefault->isEnabled('blog'))->toBeTrue()
        ->and($offByDefault->isEnabled('blog'))->toBeFalse();
});

it('treats config force-disabled modules as disabled', function (): void {
    [$manager] = makeManager([fakeModule('blog')], forcedDisabled: ['blog']);

    expect($manager->isEnabled('blog'))->toBeFalse();
});

it('refuses to enable a module whose dependency is not registered', function (): void {
    [$manager] = makeManager([fakeModule('posts', ['media'])]);

    expect(fn () => $manager->enable('posts'))
        ->toThrow(ModuleDependencyException::class);
});

it('refuses to enable a module whose dependency is disabled', function (): void {
    [$manager] = makeManager(
        [fakeModule('posts', ['media']), fakeModule('media')],
        ['media' => false],
    );

    expect(fn () => $manager->enable('posts'))
        ->toThrow(ModuleDependencyException::class);
});

it('enables a module when its dependencies are enabled', function (): void {
    [$manager] = makeManager([fakeModule('posts', ['media']), fakeModule('media')]);

    $manager->enable('posts');

    expect($manager->isEnabled('posts'))->toBeTrue();
});

it('refuses to disable a foundational module', function (): void {
    [$manager] = makeManager([fakeModule('core', foundational: true)]);

    expect(fn () => $manager->disable('core'))
        ->toThrow(ModuleDependencyException::class);
});

it('refuses to disable a module an enabled module depends on', function (): void {
    [$manager] = makeManager([fakeModule('posts', ['media']), fakeModule('media')]);

    expect(fn () => $manager->disable('media'))
        ->toThrow(ModuleDependencyException::class);
});

it('disables a module once its dependents are disabled', function (): void {
    [$manager] = makeManager(
        [fakeModule('posts', ['media']), fakeModule('media')],
        ['posts' => false],
    );

    $manager->disable('media');

    expect($manager->isEnabled('media'))->toBeFalse();
});

it('orders boot so dependencies come before dependents', function (): void {
    [$manager] = makeManager([
        fakeModule('posts', ['media']),
        fakeModule('media'),
        fakeModule('seo', ['posts']),
    ]);

    $order = $manager->bootOrder();

    expect(array_search('media', $order, true))->toBeLessThan(array_search('posts', $order, true))
        ->and(array_search('posts', $order, true))->toBeLessThan(array_search('seo', $order, true));
});

it('excludes disabled modules from the boot order', function (): void {
    [$manager] = makeManager(
        [fakeModule('posts', ['media']), fakeModule('media')],
        ['posts' => false],
    );

    expect($manager->bootOrder())->toBe(['media']);
});

it('resolves transitive dependencies', function (): void {
    [$manager] = makeManager([
        fakeModule('seo', ['posts']),
        fakeModule('posts', ['media']),
        fakeModule('media'),
    ]);

    expect($manager->dependenciesOf('seo'))->toContain('posts', 'media');
});

it('resolves transitive enabled dependents', function (): void {
    [$manager] = makeManager([
        fakeModule('seo', ['posts']),
        fakeModule('posts', ['media']),
        fakeModule('media'),
    ]);

    expect($manager->dependentsOf('media'))->toContain('posts', 'seo');
});

it('detects dependency cycles when computing boot order', function (): void {
    [$manager] = makeManager([
        fakeModule('a', ['b']),
        fakeModule('b', ['a']),
    ]);

    expect(fn () => $manager->bootOrder())
        ->toThrow(ModuleDependencyException::class);
});

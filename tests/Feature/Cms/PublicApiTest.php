<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/**
 * Every type defined under the cms-contracts namespace.
 *
 * @return array<int, class-string>
 */
function contractTypes(): array
{
    $base = base_path('packages/liberu-cms/cms-contracts/src');
    $types = [];

    foreach (Finder::create()->files()->in($base)->name('*.php') as $file) {
        $relative = str_replace(['/', '\\'], '\\', substr($file->getRelativePathname(), 0, -4));

        /** @var class-string $fqcn */
        $fqcn = 'Liberu\\Cms\\Contracts\\'.$relative;
        $types[] = $fqcn;
    }

    sort($types);

    return $types;
}

/**
 * The designation tag on a contract's docblock: 'api', 'internal', or null when
 * the type carries neither tag.
 */
function contractDesignation(string $type): ?string
{
    $doc = (new ReflectionClass($type))->getDocComment();

    if (! is_string($doc)) {
        return null;
    }

    if (str_contains($doc, '@internal')) {
        return 'internal';
    }

    return str_contains($doc, '@api') ? 'api' : null;
}

/**
 * The cms-contracts types named by a reflected parameter/return type.
 *
 * @return array<int, string>
 */
function referencedContracts(?ReflectionType $type): array
{
    if ($type instanceof ReflectionNamedType) {
        return str_starts_with($type->getName(), 'Liberu\\Cms\\Contracts\\') ? [$type->getName()] : [];
    }

    if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
        $names = [];

        foreach ($type->getTypes() as $inner) {
            $names = [...$names, ...referencedContracts($inner)];
        }

        return $names;
    }

    return [];
}

it('designates every public contract as @api or @internal', function (): void {
    $undesignated = array_values(array_filter(
        contractTypes(),
        static fn (string $type): bool => contractDesignation($type) === null,
    ));

    expect($undesignated)->toBe([]);
});

it('never leaks an @internal contract through an @api signature', function (): void {
    $designation = [];

    foreach (contractTypes() as $type) {
        $designation[$type] = contractDesignation($type);
    }

    $leaks = [];

    foreach (contractTypes() as $type) {
        if ($designation[$type] !== 'api') {
            continue;
        }

        foreach ((new ReflectionClass($type))->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() !== $type) {
                continue;
            }

            $referenced = referencedContracts($method->getReturnType());

            foreach ($method->getParameters() as $parameter) {
                $referenced = [...$referenced, ...referencedContracts($parameter->getType())];
            }

            foreach ($referenced as $name) {
                if (($designation[$name] ?? 'api') === 'internal') {
                    $leaks[] = "{$type}::{$method->getName()} exposes @internal {$name}";
                }
            }
        }
    }

    expect($leaks)->toBe([]);
});

it('exposes the core public extension contracts under their expected namespaces', function (string $type): void {
    expect(interface_exists($type) || class_exists($type) || enum_exists($type))->toBeTrue()
        ->and(contractDesignation($type))->toBe('api');
})->with([
    'Liberu\Cms\Contracts\Hooks\HookBusInterface',
    'Liberu\Cms\Contracts\Hooks\Filter',
    'Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface',
    'Liberu\Cms\Contracts\Events\EventBusInterface',
    'Liberu\Cms\Contracts\Events\CmsEvent',
    'Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface',
    'Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface',
    'Liberu\Cms\Contracts\Search\SearchRegistryInterface',
    'Liberu\Cms\Contracts\Seo\SitemapRegistryInterface',
    'Liberu\Cms\Contracts\Health\HealthCheckInterface',
    'Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface',
    'Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface',
    'Liberu\Cms\Contracts\Preview\PreviewRegistryInterface',
    'Liberu\Cms\Contracts\Block\BlockTypeRegistryInterface',
    'Liberu\Cms\Contracts\Block\BlockTypeInterface',
    'Liberu\Cms\Contracts\Widget\WidgetInterface',
    'Liberu\Cms\Contracts\Theme\ThemeManagerInterface',
    'Liberu\Cms\Contracts\Access\PermissionRegistrarInterface',
    'Liberu\Cms\Contracts\Module\ModuleInterface',
]);

<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

/**
 * Enforces the §6 dependency direction:
 *
 *   host ─▶ modules ─▶ cms-core ─▶ cms-contracts
 *
 * Arrows never point sideways (module → sibling module) or backward
 * (core → module, contracts → anything). Communication across modules happens
 * only through contracts and events, so a module's source may reference the
 * Contracts and Core namespaces (and its own), never another module's.
 */
function cmsPackageNamespaces(): array
{
    $packages = [];

    foreach (glob(base_path('modules/*'), GLOB_ONLYDIR) as $dir) {
        // The implementation indexes under modules/{features,core,api,...}
        // are documentation directories, not Composer packages.
        if (! is_file("{$dir}/composer.json")) {
            continue;
        }

        $composer = json_decode(file_get_contents("{$dir}/composer.json"), true);
        $roots = array_keys($composer['autoload']['psr-4'] ?? []);

        foreach ($roots as $root) {
            $packages[rtrim($root, '\\')] = basename($dir);
        }
    }

    return $packages;
}

/**
 * @return array<int, string> Fully-qualified Liberu\Cms imports in the file.
 */
function cmsImports(string $file): array
{
    preg_match_all('/^\s*use\s+(Liberu\\\\Cms\\\\[A-Za-z0-9_\\\\]+)/m', file_get_contents($file), $matches);

    return $matches[1] ?? [];
}

function cmsRootOf(string $fqcn, array $namespaces): ?string
{
    $best = null;

    foreach (array_keys($namespaces) as $root) {
        if (str_starts_with($fqcn, $root.'\\') && ($best === null || strlen($root) > strlen($best))) {
            $best = $root;
        }
    }

    return $best;
}

/**
 * Resolve the namespaces owned by Composer packages declared by a module.
 * Presentation adapters may import their matching core package, but never an
 * undeclared sibling. Package directories retain the historical cms-* path;
 * Composer names are the authoritative dependency identity.
 *
 * @param  array<string, string>  $namespaces
 * @return array<int, string>
 */
function cmsDeclaredDependencyNamespaces(string $package, array $namespaces): array
{
    $composerPath = base_path("modules/{$package}/composer.json");
    $composer = json_decode(file_get_contents($composerPath), true);
    $dependencies = array_keys(is_array($composer['require'] ?? null) ? $composer['require'] : []);
    $roots = [];

    foreach (glob(base_path('modules/*'), GLOB_ONLYDIR) as $dir) {
        $manifestPath = "{$dir}/composer.json";

        if (! is_file($manifestPath)) {
            continue;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);

        if (! in_array($manifest['name'] ?? null, $dependencies, true)) {
            continue;
        }

        $directory = basename($dir);
        foreach ($namespaces as $root => $owner) {
            if ($owner === $directory) {
                $roots[] = $root;
            }
        }
    }

    return $roots;
}

it('keeps module dependencies pointing only inward (no sideways or backward imports)', function (): void {
    $namespaces = cmsPackageNamespaces();
    $contracts = 'Liberu\Cms\Contracts';
    $foundations = [$contracts, 'Liberu\Cms\Core', 'Liberu\Cms\Content'];
    $violations = [];

    // A package may own several namespaces (e.g. a factories root); group them
    // so a package importing its own namespaces is never a violation.
    $ownNamespacesByPackage = [];

    foreach ($namespaces as $namespace => $package) {
        $ownNamespacesByPackage[$package][] = $namespace;
    }

    foreach ($ownNamespacesByPackage as $package => $ownNamespaces) {
        $src = base_path("modules/{$package}/src");

        if (! is_dir($src)) {
            continue;
        }

        // Any package may import contracts and its own namespaces. Only modules
        // (non-foundations) may additionally import the core/content foundations.
        $isFoundation = array_intersect($ownNamespaces, $foundations) !== [];
        $allowed = [...$ownNamespaces, $contracts];

        if (! $isFoundation) {
            $allowed = [...$allowed, ...$foundations];
        }

        $allowed = [...$allowed, ...cmsDeclaredDependencyNamespaces($package, $namespaces)];

        foreach (Finder::create()->files()->in($src)->name('*.php') as $file) {
            foreach (cmsImports($file->getRealPath()) as $import) {
                $importedRoot = cmsRootOf($import, $namespaces);

                if ($importedRoot !== null && ! in_array($importedRoot, $allowed, true)) {
                    $violations[] = "{$package} imports {$import}";
                }
            }
        }
    }

    expect($violations)->toBe([]);
});

it('never lets a module reach into the host application namespace', function (): void {
    $violations = [];

    foreach (cmsPackageNamespaces() as $package) {
        $src = base_path("modules/{$package}/src");

        if (! is_dir($src)) {
            continue;
        }

        foreach (Finder::create()->files()->in($src)->name('*.php') as $file) {
            if (preg_match('/^\s*use\s+App\\\\/m', (string) file_get_contents($file->getRealPath()))) {
                $violations[] = "{$package} imports the host App\\ namespace";
            }
        }
    }

    expect($violations)->toBe([]);
});

it('discovers the foundational CMS packages', function (): void {
    expect(cmsPackageNamespaces())
        ->toHaveKeys([
            'Liberu\Cms\Contracts',
            'Liberu\Cms\Core',
            'Liberu\Cms\Hello',
            'Liberu\Cms\Users',
        ]);
});

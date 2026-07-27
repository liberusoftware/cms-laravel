<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Liberu\Cms\Blocks\BlockTypeRegistry;
use Liberu\Cms\Contracts\Block\BlockTypeInterface;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * Files the generators write into the cms-hello module during these tests. They
 * are removed afterwards so the module (which owns real Blocks/Hooks/Fields) is
 * left untouched — only these probe files are deleted, never the directories.
 */
function generatedArtifactPaths(): array
{
    $src = base_path('packages/liberu-cms/cms-hello/src');

    return [
        "{$src}/Blocks/DemoCardBlock.php",
        "{$src}/Hooks/DemoSignalFilter.php",
        "{$src}/Hooks/DemoSignalListener.php",
        "{$src}/Fields/DemoRatingFieldType.php",
    ];
}

afterEach(function (): void {
    foreach (generatedArtifactPaths() as $path) {
        File::delete($path);
    }
});

it('scaffolds a registerable block type', function (): void {
    $this->artisan('cms:make-block', ['module' => 'hello', 'name' => 'DemoCard'])
        ->assertSuccessful();

    $path = base_path('packages/liberu-cms/cms-hello/src/Blocks/DemoCardBlock.php');
    expect(File::exists($path))->toBeTrue();

    $class = 'Liberu\\Cms\\Hello\\Blocks\\DemoCardBlock';
    expect(class_exists($class))->toBeTrue();

    $block = new $class;
    expect($block)->toBeInstanceOf(BlockTypeInterface::class)
        ->and($block->key())->toBe('demo-card')
        ->and($block->render(['text' => 'Hi']))->toContain('cms-block-demo-card', 'Hi');

    $registry = new BlockTypeRegistry;
    $registry->register($block);
    expect($registry->has('demo-card'))->toBeTrue();
});

it('scaffolds both sides of a hook', function (): void {
    $this->artisan('cms:make-hook', ['module' => 'hello', 'name' => 'DemoSignal'])
        ->assertSuccessful();

    $base = base_path('packages/liberu-cms/cms-hello/src/Hooks');
    expect(File::exists("{$base}/DemoSignalFilter.php"))->toBeTrue()
        ->and(File::exists("{$base}/DemoSignalListener.php"))->toBeTrue();

    $filterClass = 'Liberu\\Cms\\Hello\\Hooks\\DemoSignalFilter';
    $listenerClass = 'Liberu\\Cms\\Hello\\Hooks\\DemoSignalListener';
    expect(class_exists($filterClass))->toBeTrue()
        ->and(class_exists($listenerClass))->toBeTrue();

    $filter = new $filterClass('  spaced  ');
    expect($filter)->toBeInstanceOf(Filter::class)
        ->and($filter->name())->toBe('hello.demo-signal');

    (new $listenerClass)($filter);
    expect($filter->value)->toBe('spaced');
});

it('scaffolds a custom field type that registers into the registry', function (): void {
    $this->artisan('cms:make-field-type', ['module' => 'hello', 'name' => 'DemoRating'])
        ->assertSuccessful();

    $path = base_path('packages/liberu-cms/cms-hello/src/Fields/DemoRatingFieldType.php');
    expect(File::exists($path))->toBeTrue();

    $class = 'Liberu\\Cms\\Hello\\Fields\\DemoRatingFieldType';
    expect(class_exists($class))->toBeTrue();

    $registry = app(FieldTypeRegistryInterface::class);
    $class::registerInto($registry);

    expect($registry->has('demo-rating'))->toBeTrue()
        ->and($registry->options())->toHaveKey('demo-rating', 'DemoRating');
});

it('fails when the target module does not exist', function (): void {
    $this->artisan('cms:make-block', ['module' => 'nope-not-here', 'name' => 'Whatever'])
        ->assertFailed();
});

it('refuses to overwrite an existing artifact', function (): void {
    $this->artisan('cms:make-block', ['module' => 'hello', 'name' => 'DemoCard'])->assertSuccessful();

    $this->artisan('cms:make-block', ['module' => 'hello', 'name' => 'DemoCard'])
        ->assertFailed();
});

<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Liberu\Cms\Blocks\BlockTypeRegistry;
use Liberu\Cms\Contracts\Block\BlockTypeInterface;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * The generators write into a real module (cms-hello); remove what they create so
 * the package is left untouched.
 */
function helloArtifactDirs(): array
{
    $src = base_path('packages/liberu-cms/cms-hello/src');

    return ["{$src}/Blocks", "{$src}/Hooks", "{$src}/Fields"];
}

afterEach(function (): void {
    foreach (helloArtifactDirs() as $dir) {
        File::deleteDirectory($dir);
    }
});

it('scaffolds a registerable block type', function (): void {
    $this->artisan('cms:make-block', ['module' => 'hello', 'name' => 'ProbeQuote'])
        ->assertSuccessful();

    $path = base_path('packages/liberu-cms/cms-hello/src/Blocks/ProbeQuoteBlock.php');
    expect(File::exists($path))->toBeTrue();

    $class = 'Liberu\\Cms\\Hello\\Blocks\\ProbeQuoteBlock';
    expect(class_exists($class))->toBeTrue();

    $block = new $class;
    expect($block)->toBeInstanceOf(BlockTypeInterface::class)
        ->and($block->key())->toBe('probe-quote')
        ->and($block->render(['text' => 'Hi']))->toContain('cms-block-probe-quote', 'Hi');

    $registry = new BlockTypeRegistry;
    $registry->register($block);
    expect($registry->has('probe-quote'))->toBeTrue();
});

it('scaffolds both sides of a hook', function (): void {
    $this->artisan('cms:make-hook', ['module' => 'hello', 'name' => 'Greeting'])
        ->assertSuccessful();

    $base = base_path('packages/liberu-cms/cms-hello/src/Hooks');
    expect(File::exists("{$base}/GreetingFilter.php"))->toBeTrue()
        ->and(File::exists("{$base}/GreetingListener.php"))->toBeTrue();

    $filterClass = 'Liberu\\Cms\\Hello\\Hooks\\GreetingFilter';
    $listenerClass = 'Liberu\\Cms\\Hello\\Hooks\\GreetingListener';
    expect(class_exists($filterClass))->toBeTrue()
        ->and(class_exists($listenerClass))->toBeTrue();

    $filter = new $filterClass('  spaced  ');
    expect($filter)->toBeInstanceOf(Filter::class)
        ->and($filter->name())->toBe('hello.greeting');

    (new $listenerClass)($filter);
    expect($filter->value)->toBe('spaced');
});

it('scaffolds a custom field type that registers into the registry', function (): void {
    $this->artisan('cms:make-field-type', ['module' => 'hello', 'name' => 'Shade'])
        ->assertSuccessful();

    $path = base_path('packages/liberu-cms/cms-hello/src/Fields/ShadeFieldType.php');
    expect(File::exists($path))->toBeTrue();

    $class = 'Liberu\\Cms\\Hello\\Fields\\ShadeFieldType';
    expect(class_exists($class))->toBeTrue();

    $registry = app(FieldTypeRegistryInterface::class);
    $class::registerInto($registry);

    expect($registry->has('shade'))->toBeTrue()
        ->and($registry->options())->toHaveKey('shade', 'Shade');
});

it('fails when the target module does not exist', function (): void {
    $this->artisan('cms:make-block', ['module' => 'nope-not-here', 'name' => 'Whatever'])
        ->assertFailed();
});

it('refuses to overwrite an existing artifact', function (): void {
    $this->artisan('cms:make-block', ['module' => 'hello', 'name' => 'ProbeQuote'])->assertSuccessful();

    $this->artisan('cms:make-block', ['module' => 'hello', 'name' => 'ProbeQuote'])
        ->assertFailed();
});

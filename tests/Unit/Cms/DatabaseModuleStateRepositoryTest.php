<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Liberu\Cms\Core\Module\DatabaseModuleStateRepository;

uses(RefreshDatabase::class);

test('it reads, caches, writes, and forgets module state', function (): void {
    DB::table('cms_modules')->insert(['key' => 'blog', 'enabled' => false]);
    $repository = new DatabaseModuleStateRepository(DB::getFacadeRoot());

    expect($repository->isEnabled('blog'))->toBeFalse();

    DB::table('cms_modules')->where('key', 'blog')->update(['enabled' => true]);
    expect($repository->isEnabled('blog'))->toBeFalse();

    $repository->setEnabled('blog', true);
    expect($repository->isEnabled('blog'))->toBeTrue();

    $repository->forget('blog');
    expect($repository->isEnabled('blog', false))->toBeFalse()
        ->and(DB::table('cms_modules')->where('key', 'blog')->exists())->toBeFalse();
});

test('it normalizes common database boolean representations', function (mixed $value, bool $expected): void {
    $repository = new DatabaseModuleStateRepository(DB::getFacadeRoot());
    $method = new ReflectionMethod($repository, 'normalizeBool');

    expect($method->invoke($repository, $value))->toBe($expected);
})->with([
    [0, false], ['0', false], ['f', false], ['false', false], ['off', false], ['no', false], ['', false],
    [1, true], ['1', true], ['t', true], ['true', true], ['on', true], ['yes', true],
    [true, true], [false, false],
]);

test('it falls back safely when the module table is not available', function (): void {
    Schema::drop('cms_modules');
    $repository = new DatabaseModuleStateRepository(DB::getFacadeRoot());

    expect($repository->isEnabled('blog', false))->toBeFalse();

    $repository->setEnabled('blog', true);
    $repository->forget('blog');
    expect($repository->isEnabled('blog', true))->toBeTrue();
});

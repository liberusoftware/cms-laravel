<?php

declare(strict_types=1);

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Schema\Builder;
use Liberu\Cms\Core\Module\DatabaseModuleStateRepository;
use Liberu\Cms\Themes\ThemeStateRepository;

it('handles database module state connection failures safely', function (): void {
    $resolver = Mockery::mock(ConnectionResolverInterface::class);
    $resolver->shouldReceive('connection')->andReturn(new stdClass);

    $repository = new DatabaseModuleStateRepository($resolver);

    expect($repository->isEnabled('missing', false))->toBeFalse();
    $repository->setEnabled('missing', true);
    $repository->forget('missing');
});

it('handles database module state query failures safely', function (): void {
    $schema = Mockery::mock(Builder::class);
    $schema->shouldReceive('hasTable')->with('cms_modules')->andReturnTrue();
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);
    $connection->shouldReceive('table')->with('cms_modules')->andThrow(new RuntimeException('database unavailable'));
    $resolver = Mockery::mock(ConnectionResolverInterface::class);
    $resolver->shouldReceive('connection')->andReturn($connection);

    expect((new DatabaseModuleStateRepository($resolver))->isEnabled('missing', false))->toBeFalse();
});

it('handles theme state connection failures safely', function (): void {
    $resolver = Mockery::mock(ConnectionResolverInterface::class);
    $resolver->shouldReceive('connection')->andReturn(new stdClass);

    $repository = new ThemeStateRepository($resolver);

    expect($repository->activeKey())->toBeNull();
    $repository->setActiveKey('default');
});

it('handles theme state query failures safely', function (): void {
    $schema = Mockery::mock(Builder::class);
    $schema->shouldReceive('hasTable')->with('cms_theme_state')->andReturnTrue();
    $connection = Mockery::mock(Connection::class);
    $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);
    $connection->shouldReceive('table')->with('cms_theme_state')->andThrow(new RuntimeException('database unavailable'));
    $resolver = Mockery::mock(ConnectionResolverInterface::class);
    $resolver->shouldReceive('connection')->andReturn($connection);

    expect((new ThemeStateRepository($resolver))->activeKey())->toBeNull();
});

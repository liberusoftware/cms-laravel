<?php

declare(strict_types=1);

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;
use Liberu\Cms\Media\Health\StorageHealthCheck;
use Liberu\Cms\Observability\Health\HealthCheckRegistry;
use RuntimeException;

it('reports storage healthy when the disk is writable', function (): void {
    Storage::fake('public');

    $check = new StorageHealthCheck(app(FilesystemFactory::class), 'public', critical: false);

    expect($check->name())->toBe('storage')
        ->and($check->isCritical())->toBeFalse()
        ->and($check->check())->toBeTrue();
});

it('reports storage fail when the disk cannot be written', function (): void {
    $disk = Mockery::mock(Filesystem::class);
    $disk->shouldReceive('put')->andThrow(new RuntimeException('disk full'));

    $factory = Mockery::mock(FilesystemFactory::class);
    $factory->shouldReceive('disk')->with('public')->andReturn($disk);

    $check = new StorageHealthCheck($factory, 'public', critical: false);

    expect($check->check())->toBeFalse();
});

it('contributes the storage check to the readiness probe', function (): void {
    $names = collect($this->getJson('/health/ready')->json('checks'))->pluck('name');

    expect($names)->toContain('storage');
});

it('degrades readiness to 200 when media storage is down', function (): void {
    $disk = Mockery::mock(Filesystem::class);
    $disk->shouldReceive('put')->andThrow(new RuntimeException('disk full'));

    $factory = Mockery::mock(FilesystemFactory::class);
    $factory->shouldReceive('disk')->andReturn($disk);

    $registry = new HealthCheckRegistry;
    $registry->register(new StorageHealthCheck($factory, 'public', critical: false));
    app()->instance(HealthCheckRegistryInterface::class, $registry);

    $this->getJson('/health/ready')
        ->assertOk()
        ->assertJson([
            'status' => 'degraded',
            'checks' => [['name' => 'storage', 'status' => 'fail']],
        ]);
});

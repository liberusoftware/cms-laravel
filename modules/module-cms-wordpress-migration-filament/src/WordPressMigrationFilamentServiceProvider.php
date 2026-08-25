<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\WordPressMigrationFilament\Resources\WordPressMigrationResource;

final class WordPressMigrationFilamentServiceProvider extends ServiceProvider { public function register(): void { if ($this->app->bound(AdminResourceRegistryInterface::class)) $this->app->make(AdminResourceRegistryInterface::class)->registerResource('wordpress-migration', WordPressMigrationResource::class); } }

<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AuditAndHistoryFilament\Resources\AuditLogResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class AuditAndHistoryFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('audit-and-history', AuditLogResource::class);
        }
    }
}

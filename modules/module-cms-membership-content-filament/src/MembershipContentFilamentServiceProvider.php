<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\MembershipContentFilament\Resources\MembershipContentResource;

final class MembershipContentFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('membership-content', MembershipContentResource::class);
        }
    }
}

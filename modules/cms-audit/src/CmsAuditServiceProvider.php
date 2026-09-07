<?php

declare(strict_types=1);

namespace Liberu\Cms\Audit;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Events\Dispatcher;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

/**
 * Wires the Audit module: the append-only logger, the read-only admin viewer,
 * and the event subscriptions that turn security-relevant events into audit
 * records. Listeners are registered only while the module is enabled, so
 * disabling it stops auditing entirely.
 */
final class CmsAuditServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CmsAuditModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(AuditLogger::class);
        $this->app->singleton(AuditSubscriber::class);

    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        $this->subscribe();

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('audit', 'Audit log', AccessScope::Module, ['view']),
            );
        }
    }

    /**
     * Register listeners on both seams: content events cross the EventBus, while
     * authentication events are framework events on the underlying dispatcher.
     */
    private function subscribe(): void
    {
        $subscriber = $this->app->make(AuditSubscriber::class);

        $bus = $this->app->make(EventBusInterface::class);
        $bus->listen(ContentPublished::class, fn (ContentPublished $event) => $subscriber->handleContentPublished($event));
        $bus->listen(ContentStateChanged::class, fn (ContentStateChanged $event) => $subscriber->handleContentStateChanged($event));

        $events = $this->app->make(Dispatcher::class);
        $events->listen(Login::class, fn (Login $event) => $subscriber->handleLogin($event));
        $events->listen(Logout::class, fn (Logout $event) => $subscriber->handleLogout($event));
        $events->listen(Failed::class, fn (Failed $event) => $subscriber->handleFailed($event));
    }
}

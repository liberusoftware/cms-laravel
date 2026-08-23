<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Form\FormSubmitted;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Notifications\Channels\ChannelManager;
use Liberu\Cms\Notifications\Channels\LogChannel;
use Liberu\Cms\Notifications\Channels\MailChannel;
use Liberu\Cms\Notifications\Filament\NotificationLogResource;

/**
 * Wires Notifications: the channel manager (with the built-in mail and log
 * channels), the dispatcher, and the event-bus subscriptions that turn CMS
 * events into notifications.
 */
final class CmsNotificationsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CmsNotificationsModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/notifications.php', 'cms-notifications');

        $this->app->singleton(ChannelManager::class, fn (): ChannelManager => new ChannelManager([
            $this->app->make(MailChannel::class),
            $this->app->make(LogChannel::class),
        ]));

        $this->app->singleton(NotificationDispatcher::class);

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)
                ->registerResource('notifications', NotificationLogResource::class);
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        $this->subscribe(
            $this->app->make(EventBusInterface::class),
            $this->app->make(NotificationDispatcher::class),
        );

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('notification-logs', 'Notification logs', AccessScope::Module, ['view', 'delete']),
            );
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/notifications.php' => $this->app->configPath('cms-notifications.php'),
            ], 'cms-notifications-config');
        }
    }

    private function subscribe(EventBusInterface $bus, NotificationDispatcher $dispatcher): void
    {
        $bus->listen(FormSubmitted::class, function (FormSubmitted $event) use ($dispatcher): void {
            $dispatcher->dispatch($event->name(), $event->teamId, [
                'formSlug' => $event->formSlug,
                'submissionId' => $event->submissionId,
                'data' => $event->data,
            ]);
        });

        $bus->listen(ContentPublished::class, function (ContentPublished $event) use ($dispatcher): void {
            $dispatcher->dispatch($event->name(), null, [
                'contentType' => $event->contentType,
                'contentId' => $event->contentId,
            ]);
        });
    }
}

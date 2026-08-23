<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Block\BlockTypeRegistryInterface;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Contracts\Hooks\Filters\BlockRenderFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Hello\Blocks\GreetingBlock;
use Liberu\Cms\Hello\Contracts\GreeterInterface;
use Liberu\Cms\Hello\Fields\GreetingFieldType;
use Liberu\Cms\Hello\Filament\GreetingResource;
use Liberu\Cms\Hello\Http\Controllers\GreetingApiController;
use Liberu\Cms\Hello\Services\Greeter;

/**
 * The reference extension. Beyond the original greeter, it exercises the whole
 * public extension surface: an admin resource, a Delivery API endpoint, a block
 * type, a custom field kind, a consumed core filter, and its own filter point —
 * each wired through a contract and guarded by a bound() check so the module
 * stays removable and embeddable.
 */
final class HelloServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new HelloModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/hello.php', 'hello');

        $this->app->bind(GreeterInterface::class, function (): Greeter {
            $greeting = config('hello.greeting', 'Hello, :name!');

            return new Greeter(is_string($greeting) ? $greeting : 'Hello, :name!');
        });

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)
                ->registerResource('hello', GreetingResource::class);
        }

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'hello',
                new ApiEndpoint('greetings', GreetingApiController::class, 'index', 'hello.greetings'),
            );
        }

        if ($this->app->bound(FieldTypeRegistryInterface::class)) {
            GreetingFieldType::registerInto($this->app->make(FieldTypeRegistryInterface::class));
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        $this->loadModuleRoutesFrom(__DIR__.'/../routes/api.php');

        $this->publishes([
            __DIR__.'/../config/hello.php' => $this->app->configPath('hello.php'),
        ], 'cms-hello-config');

        if ($this->app->bound(BlockTypeRegistryInterface::class)) {
            $this->app->make(BlockTypeRegistryInterface::class)->register(new GreetingBlock);
        }

        // Consume a core filter point: tag this extension's own block output.
        $this->app->make(HookBusInterface::class)->listen(
            BlockRenderFilter::class,
            static function (BlockRenderFilter $filter): void {
                if ($filter->blockType === 'greeting') {
                    $filter->html .= '<!-- greeted by cms-hello -->';
                }
            },
        );

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('hello', 'Hello', AccessScope::Content, ['view', 'create', 'update', 'delete']),
            );
        }
    }
}

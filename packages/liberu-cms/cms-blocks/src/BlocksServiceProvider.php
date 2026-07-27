<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks;

use Liberu\Cms\Blocks\Types\CodeBlock;
use Liberu\Cms\Blocks\Types\ColumnsBlock;
use Liberu\Cms\Blocks\Types\CtaBlock;
use Liberu\Cms\Blocks\Types\HeadingBlock;
use Liberu\Cms\Blocks\Types\ImageBlock;
use Liberu\Cms\Blocks\Types\TextBlock;
use Liberu\Cms\Contracts\Block\BlockRendererInterface;
use Liberu\Cms\Contracts\Block\BlockTypeRegistryInterface;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class BlocksServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new BlocksModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(BlockTypeRegistry::class);
        $this->app->alias(BlockTypeRegistry::class, BlockTypeRegistryInterface::class);

        $this->app->singleton(BlockRendererInterface::class, fn (): BlockRenderer => new BlockRenderer(
            $this->app->make(BlockTypeRegistry::class),
            $this->app->make(HookBusInterface::class),
        ));
    }

    protected function bootModule(): void
    {
        $registry = $this->app->make(BlockTypeRegistry::class);

        $registry->register(new TextBlock);
        $registry->register(new HeadingBlock);
        $registry->register(new ImageBlock);
        $registry->register(new CodeBlock);
        $registry->register(new CtaBlock);
        $registry->register(new ColumnsBlock);
    }
}

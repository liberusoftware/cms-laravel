<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts;

use Liberu\Cms\Core\Module\AbstractModule;

final class PostsModule extends AbstractModule
{
    public function key(): string
    {
        return 'posts';
    }

    public function name(): string
    {
        return 'Posts';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    #[\Override]
    public function dependencies(): array
    {
        return ['media'];
    }
}

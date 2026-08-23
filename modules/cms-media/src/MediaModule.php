<?php

declare(strict_types=1);

namespace Liberu\Cms\Media;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Media library. A dependency of the content modules; not foundational —
 * content modules declare it as a dependency, so it cannot be disabled out from
 * under them, but a media-less install may remove it.
 */
final class MediaModule extends AbstractModule
{
    public function key(): string
    {
        return 'media';
    }

    public function name(): string
    {
        return 'Media';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

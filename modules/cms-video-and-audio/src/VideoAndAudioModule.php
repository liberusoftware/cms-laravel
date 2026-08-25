<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio;

use Liberu\Cms\Core\Module\AbstractModule;

final class VideoAndAudioModule extends AbstractModule
{
    public function key(): string
    {
        return 'video-and-audio';
    }

    public function name(): string
    {
        return 'Video and Audio';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

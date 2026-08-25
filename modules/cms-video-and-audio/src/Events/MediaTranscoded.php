<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Events;

use Liberu\Cms\VideoAndAudio\Models\MediaVariant;

final readonly class MediaTranscoded
{
    public function __construct(public MediaVariant $variant) {}
}

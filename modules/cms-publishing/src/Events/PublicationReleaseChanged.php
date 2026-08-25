<?php

declare(strict_types=1);

namespace Liberu\Cms\Publishing\Events;

use Liberu\Cms\Publishing\Models\PublicationRelease;

final readonly class PublicationReleaseChanged
{
    public function __construct(public PublicationRelease $release, public string $event) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Core;

use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * @api This event is part of the public CMS extension API.
 */
final readonly class ContentIdentityCreated implements CmsEvent
{
    public function __construct(public int|string $identityId, public string $contentType, public string $contentId) {}

    public function name(): string
    {
        return 'core.content-identity.created';
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Core;

use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * @api This event is part of the public CMS extension API.
 */
final readonly class SettingChanged implements CmsEvent
{
    public function __construct(public int|string $settingId, public int|string|null $siteId, public string $key) {}

    public function name(): string
    {
        return 'core.setting.changed';
    }
}

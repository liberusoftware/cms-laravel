<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes\Exceptions;

use RuntimeException;

final class UnknownTheme extends RuntimeException
{
    public static function key(string $key): self
    {
        return new self("Theme [{$key}] is not registered.");
    }
}

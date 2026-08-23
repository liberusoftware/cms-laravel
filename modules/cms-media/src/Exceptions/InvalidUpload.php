<?php

declare(strict_types=1);

namespace Liberu\Cms\Media\Exceptions;

use RuntimeException;

final class InvalidUpload extends RuntimeException
{
    public static function corrupt(): self
    {
        return new self('The uploaded file is invalid or could not be stored.');
    }

    public static function tooLarge(int $sizeKb, int $maxKb): self
    {
        return new self("The file is {$sizeKb} KB, which exceeds the {$maxKb} KB limit.");
    }

    public static function disallowedType(string $mimeType): self
    {
        return new self("Files of type [{$mimeType}] are not allowed.");
    }
}

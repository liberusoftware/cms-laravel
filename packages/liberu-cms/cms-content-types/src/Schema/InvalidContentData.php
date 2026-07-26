<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Schema;

use Liberu\Cms\ContentTypes\Fields\FieldType;
use RuntimeException;

final class InvalidContentData extends RuntimeException
{
    private function __construct(string $message, public readonly string $field)
    {
        parent::__construct($message);
    }

    public static function missingRequired(string $field): self
    {
        return new self("The field [{$field}] is required.", $field);
    }

    public static function wrongType(string $field, FieldType $type): self
    {
        return new self("The field [{$field}] must be of type [{$type->value}].", $field);
    }
}

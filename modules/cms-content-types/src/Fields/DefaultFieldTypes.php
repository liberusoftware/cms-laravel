<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Fields;

use Liberu\Cms\Contracts\Fields\FieldTypeDefinition;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

/**
 * Seeds the field kinds the CMS ships with. Each is an ordinary
 * {@see FieldTypeDefinition} — built-ins hold no privileged status over kinds a
 * third party registers. The component factory returns the base edit component;
 * the resource applies the field's label and required flag.
 */
final class DefaultFieldTypes
{
    public static function registerInto(FieldTypeRegistryInterface $registry): void
    {
        $string = static fn (mixed $value): bool => is_string($value);

        $registry->register(new FieldTypeDefinition(
            'text',
            'Text',
            static fn (string $path, array $options): object => new \stdClass,
            $string,
        ));

        $registry->register(new FieldTypeDefinition(
            'textarea',
            'Textarea',
            static fn (string $path, array $options): object => new \stdClass,
            $string,
        ));

        $registry->register(new FieldTypeDefinition(
            'richtext',
            'Richtext',
            static fn (string $path, array $options): object => new \stdClass,
            $string,
        ));

        $registry->register(new FieldTypeDefinition(
            'number',
            'Number',
            static fn (string $path, array $options): object => new \stdClass,
            static fn (mixed $value): bool => is_int($value) || is_float($value) || (is_string($value) && is_numeric($value)),
        ));

        $registry->register(new FieldTypeDefinition(
            'boolean',
            'Boolean',
            static fn (string $path, array $options): object => new \stdClass,
            static fn (mixed $value): bool => is_bool($value),
        ));

        $registry->register(new FieldTypeDefinition(
            'date',
            'Date',
            static fn (string $path, array $options): object => new \stdClass,
            $string,
        ));

        $registry->register(new FieldTypeDefinition(
            'select',
            'Select',
            static fn (string $path, array $options): object => new \stdClass,
            $string,
        ));

        $registry->register(new FieldTypeDefinition(
            'media',
            'Media',
            static fn (string $path, array $options): object => new \stdClass,
            static fn (mixed $value): bool => is_int($value),
        ));
    }
}

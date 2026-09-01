<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesFilament\Fields;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Liberu\Cms\Contracts\Fields\FieldTypeDefinition;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

final class DefaultFilamentFieldTypes
{
    public static function registerInto(FieldTypeRegistryInterface $registry): void
    {
        $string = static fn (mixed $value): bool => is_string($value);
        $registry->register(new FieldTypeDefinition('text', 'Text', static fn (string $path, array $options): TextInput => TextInput::make($path)->maxLength(255), $string));
        $registry->register(new FieldTypeDefinition('textarea', 'Textarea', static fn (string $path, array $options): Textarea => Textarea::make($path)->rows(5), $string));
        $registry->register(new FieldTypeDefinition('richtext', 'Richtext', static fn (string $path, array $options): Textarea => Textarea::make($path)->rows(5), $string));
        $registry->register(new FieldTypeDefinition('number', 'Number', static fn (string $path, array $options): TextInput => TextInput::make($path)->numeric(), static fn (mixed $value): bool => is_int($value) || is_float($value) || (is_string($value) && is_numeric($value))));
        $registry->register(new FieldTypeDefinition('boolean', 'Boolean', static fn (string $path, array $options): Toggle => Toggle::make($path), static fn (mixed $value): bool => is_bool($value)));
        $registry->register(new FieldTypeDefinition('date', 'Date', static fn (string $path, array $options): DatePicker => DatePicker::make($path), $string));
        $registry->register(new FieldTypeDefinition('select', 'Select', static fn (string $path, array $options): Select => Select::make($path)->options(array_combine($options, $options)), $string));
        $registry->register(new FieldTypeDefinition('media', 'Media', static fn (string $path, array $options): TextInput => TextInput::make($path)->numeric()->helperText('Media ID'), static fn (mixed $value): bool => is_int($value)));
    }
}

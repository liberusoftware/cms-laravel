<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Fields;

/**
 * The input types a form field may declare. Each maps to a Laravel validation
 * rule fragment applied to the submitted value.
 */
enum FormFieldType: string
{
    case Text = 'text';
    case Email = 'email';
    case Textarea = 'textarea';
    case Number = 'number';
    case Checkbox = 'checkbox';

    public function rule(): string
    {
        return match ($this) {
            self::Email => 'email',
            self::Number => 'numeric',
            self::Checkbox => 'boolean',
            self::Text, self::Textarea => 'string',
        };
    }

    /**
     * The types as value => human label pairs, for admin form selects.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $type) {
            $options[$type->value] = ucfirst($type->value);
        }

        return $options;
    }
}

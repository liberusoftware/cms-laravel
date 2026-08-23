<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Fields;

/**
 * A single field in a form's schema: the submitted key, its label, input type,
 * and whether it is required.
 */
final readonly class FormField
{
    public function __construct(
        public string $name,
        public string $label,
        public FormFieldType $type,
        public bool $required = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $name = $data['name'] ?? '';
        $label = $data['label'] ?? '';
        $type = $data['type'] ?? 'text';

        return new self(
            name: is_string($name) ? $name : '',
            label: is_string($label) ? $label : '',
            type: (is_string($type) ? FormFieldType::tryFrom($type) : null) ?? FormFieldType::Text,
            required: (bool) ($data['required'] ?? false),
        );
    }
}

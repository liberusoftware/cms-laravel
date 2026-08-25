<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Fields;

/**
 * One field in a content type's schema. `type` is a field-kind key resolved
 * through the FieldTypeRegistry (e.g. "text", "number", or a custom "color"),
 * never a fixed enum, so third-party kinds are first-class.
 */
final readonly class FieldDefinition
{
    /**
     * @param  list<string>  $options  Choices for a Select field.
     */
    public function __construct(
        public string $name,
        public string $label,
        public string $type,
        public bool $required = false,
        public array $options = [],
        public string $cardinality = 'one',
        public mixed $default = null,
        public array $validation = [],
        public bool $computed = false,
        public ?array $condition = null,
        public ?string $group = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $type = $data['type'] ?? null;
        $options = $data['options'] ?? null;

        return new self(
            name: is_string($data['name'] ?? null) ? $data['name'] : '',
            label: is_string($data['label'] ?? null) ? $data['label'] : '',
            type: is_string($type) ? $type : 'text',
            required: (bool) ($data['required'] ?? false),
            options: is_array($options) ? array_values(array_filter($options, is_string(...))) : [],
            cardinality: ($data['cardinality'] ?? 'one') === 'many' ? 'many' : 'one',
            default: $data['default'] ?? null,
            validation: is_array($data['validation'] ?? null) ? $data['validation'] : [],
            computed: (bool) ($data['computed'] ?? false),
            condition: is_array($data['condition'] ?? null) ? $data['condition'] : null,
            group: is_string($data['group'] ?? null) && $data['group'] !== '' ? $data['group'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'required' => $this->required,
            'options' => $this->options,
        ];

        if ($this->cardinality !== 'one') {
            $result['cardinality'] = $this->cardinality;
        }
        if ($this->default !== null) {
            $result['default'] = $this->default;
        }
        if ($this->validation !== []) {
            $result['validation'] = $this->validation;
        }
        if ($this->computed) {
            $result['computed'] = true;
        }
        if ($this->condition !== null) {
            $result['condition'] = $this->condition;
        }
        if ($this->group !== null) {
            $result['group'] = $this->group;
        }

        return $result;
    }
}

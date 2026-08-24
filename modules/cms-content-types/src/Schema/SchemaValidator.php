<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Schema;

use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Fields\FieldTypeDefinition;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

/**
 * Validates a content entry's data against its type's field schema: required
 * fields must be present, values must roughly match their declared kind (as
 * defined in the FieldTypeRegistry), and fields not in the schema are dropped.
 */
final readonly class SchemaValidator
{
    public function __construct(private FieldTypeRegistryInterface $registry) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed> The data limited to the schema's fields.
     */
    public function validate(ContentType $type, array $data): array
    {
        $validated = [];

        foreach ($type->fieldDefinitions() as $field) {
            if ($field->computed || ! $this->conditionMatches($field->condition, $data)) {
                continue;
            }

            $value = array_key_exists($field->name, $data) ? $data[$field->name] : $field->default;

            if ($field->required && ($value === null || $value === '')) {
                throw InvalidContentData::missingRequired($field->name);
            }

            if ($value !== null && $field->cardinality === 'many' && ! is_array($value)) {
                throw InvalidContentData::wrongType($field->name, 'array');
            }

            $values = $field->cardinality === 'many' ? $value : [$value];
            if ($value !== null && ! $this->matchesValues($field->type, $values)) {
                throw InvalidContentData::wrongType($field->name, $field->type);
            }

            $this->validateRules($field->name, $value, $field->validation);
            if (array_key_exists($field->name, $data) || $field->default !== null) {
                $validated[$field->name] = $value;
            }
        }

        return $validated;
    }

    private function matchesType(string $type, mixed $value): bool
    {
        $definition = $this->registry->get($type);

        return $definition instanceof FieldTypeDefinition && ($definition->matches)($value);
    }

    /** @param list<mixed> $values */
    private function matchesValues(string $type, array $values): bool
    {
        foreach ($values as $value) {
            if (! $this->matchesType($type, $value)) {
                return false;
            }
        }

        return true;
    }

    /** @param array<string, mixed>|null $condition */
    private function conditionMatches(?array $condition, array $data): bool
    {
        if ($condition === null || ! is_string($condition['field'] ?? null)) {
            return true;
        }

        return ($data[$condition['field']] ?? null) === ($condition['equals'] ?? null);
    }

    /** @param array<string, mixed> $rules */
    private function validateRules(string $field, mixed $value, array $rules): void
    {
        if ($value === null) {
            return;
        }
        if (isset($rules['min']) && is_numeric($value) && $value < $rules['min']) {
            throw InvalidContentData::ruleFailed($field, 'minimum');
        }
        if (isset($rules['max']) && is_numeric($value) && $value > $rules['max']) {
            throw InvalidContentData::ruleFailed($field, 'maximum');
        }
        if (isset($rules['minItems']) && is_array($value) && count($value) < (int) $rules['minItems']) {
            throw InvalidContentData::ruleFailed($field, 'minimum item count');
        }
        if (isset($rules['maxItems']) && is_array($value) && count($value) > (int) $rules['maxItems']) {
            throw InvalidContentData::ruleFailed($field, 'maximum item count');
        }
    }
}

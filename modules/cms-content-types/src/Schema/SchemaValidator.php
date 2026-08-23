<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Schema;

use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

/**
 * Validates a content entry's data against its type's field schema: required
 * fields must be present, values must roughly match their declared kind (as
 * defined in the FieldTypeRegistry), and fields not in the schema are dropped.
 */
final class SchemaValidator
{
    public function __construct(private readonly FieldTypeRegistryInterface $registry) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed> The data limited to the schema's fields.
     */
    public function validate(ContentType $type, array $data): array
    {
        $names = [];

        foreach ($type->fieldDefinitions() as $field) {
            $names[] = $field->name;
            $value = $data[$field->name] ?? null;

            if ($field->required && ($value === null || $value === '')) {
                throw InvalidContentData::missingRequired($field->name);
            }

            if ($value !== null && ! $this->matchesType($field->type, $value)) {
                throw InvalidContentData::wrongType($field->name, $field->type);
            }
        }

        return array_intersect_key($data, array_flip($names));
    }

    private function matchesType(string $type, mixed $value): bool
    {
        $definition = $this->registry->get($type);

        return $definition !== null && ($definition->matches)($value);
    }
}

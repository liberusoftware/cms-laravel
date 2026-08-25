<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Queries;

use Liberu\Cms\ContentTypes\Fields\FieldDefinition;
use Liberu\Cms\ContentTypes\Models\ContentType;

/**
 * Public read model for field schemas. Presentation packages consume this
 * query instead of coupling themselves to the ContentType persistence model.
 */
final class FieldSchemaQuery
{
    /**
     * @return array{key:string,version:int,fields:list<array<string,mixed>>}|null
     */
    public function find(string $key): ?array
    {
        $type = ContentType::query()->where('key', $key)->first();

        if (! $type instanceof ContentType) {
            return null;
        }

        return [
            'key' => $type->key,
            'version' => (int) ($type->schema_version ?? 1),
            'fields' => array_map(
                static fn (FieldDefinition $field): array => $field->toArray(),
                $type->fieldDefinitions(),
            ),
        ];
    }
}

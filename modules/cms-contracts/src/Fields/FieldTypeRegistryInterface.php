<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Fields;

/**
 * The catalogue of content-type field kinds. The content-types module seeds the
 * built-in kinds; any module may register its own so custom kinds appear in the
 * schema editor, validate, and render like a built-in. The validator and the
 * admin form resolve kinds through this registry, never a fixed enum.
 *
 * @api This interface is part of the public extension API.
 */
interface FieldTypeRegistryInterface
{
    /**
     * @param  FieldTypeDefinition<object>  $definition
     */
    public function register(FieldTypeDefinition $definition): void;

    /**
     * @return FieldTypeDefinition<object>|null
     */
    public function get(string $key): ?FieldTypeDefinition;

    public function has(string $key): bool;

    /**
     * @return array<string, FieldTypeDefinition<object>>
     */
    public function all(): array;

    /**
     * The registered kinds as key => label pairs, for schema-editor selects.
     *
     * @return array<string, string>
     */
    public function options(): array;
}

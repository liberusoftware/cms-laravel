<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Fields;

use Liberu\Cms\Contracts\Fields\FieldTypeDefinition;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

/**
 * In-memory catalogue of field kinds, keyed by their stable key. Mirrors the
 * block-type registry: modules register their kinds, and the validator and admin
 * form resolve them by key.
 */
final class FieldTypeRegistry implements FieldTypeRegistryInterface
{
    /**
     * @var array<string, FieldTypeDefinition<object>>
     */
    private array $types = [];

    public function register(FieldTypeDefinition $definition): void
    {
        $this->types[$definition->key] = $definition;
    }

    public function get(string $key): ?FieldTypeDefinition
    {
        return $this->types[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->types[$key]);
    }

    public function all(): array
    {
        return $this->types;
    }

    public function options(): array
    {
        return array_map(static fn (FieldTypeDefinition $definition): string => $definition->label, $this->types);
    }
}

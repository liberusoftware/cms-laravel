<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystem\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\FieldSystem\Models\FieldSchema;

final readonly class FieldSystemService
{
    public function __construct(private FieldTypeRegistryInterface $types) {}

    public function saveSchema(?int $teamId, string $key, string $name, array $fields, ?string $reason = null): FieldSchema
    {
        $this->validateDefinitions($fields);
        if (! preg_match('/^[a-z0-9][a-z0-9_-]{0,254}$/', $key) || trim($name) === '') {
            throw ValidationException::withMessages(['schema' => 'Schema key and name must be valid.']);
        }

        return DB::transaction(function () use ($teamId, $key, $name, $fields, $reason): FieldSchema {
            $schema = FieldSchema::query()->where('team_id', $teamId)->where('key', $key)->lockForUpdate()->first();
            $version = ($schema?->version ?? 0) + 1;
            $history = $schema?->history ?? [];
            if ($schema) {
                $history[] = ['version' => $schema->version, 'fields' => $schema->fields, 'reason' => $reason, 'created_at' => now()->toISOString()];
            }

            return FieldSchema::query()->updateOrCreate(['team_id' => $teamId, 'key' => $key], ['name' => trim($name), 'version' => $version, 'fields' => $fields, 'history' => $history]);
        });
    }

    /** @return array<string, mixed> */
    public function validateData(FieldSchema $schema, array $data): array
    {
        $validated = [];
        foreach ($schema->fields ?? [] as $field) {
            if (($field['computed'] ?? false) === true) {
                continue;
            }
            if (! is_array($field) || ! is_string($field['name'] ?? null)) {
                continue;
            }
            $name = $field['name'];
            if (($field['condition']['field'] ?? null) !== null && (($data[$field['condition']['field']] ?? null) !== ($field['condition']['equals'] ?? null))) {
                continue;
            }
            $value = array_key_exists($name, $data) ? $data[$name] : ($field['default'] ?? null);
            if (($field['required'] ?? false) && ($value === null || $value === '')) {
                throw ValidationException::withMessages([$name => 'This field is required.']);
            }
            if (($field['cardinality'] ?? 'one') === 'many' && $value !== null && ! is_array($value)) {
                throw ValidationException::withMessages([$name => 'This field must contain multiple values.']);
            }
            $values = ($field['cardinality'] ?? 'one') === 'many' ? ($value ?? []) : [$value];
            foreach ($values as $item) {
                if ($item !== null && $this->types->get((string) ($field['type'] ?? ''))?->matches && ! ($this->types->get((string) $field['type'])->matches)($item)) {
                    throw ValidationException::withMessages([$name => 'The field value has the wrong type.']);
                }
            }
            if (array_key_exists($name, $data) || $value !== null) {
                $validated[$name] = $value;
            }
        }

        return $validated;
    }

    private function validateDefinitions(array $fields): void
    {
        $names = [];
        foreach ($fields as $field) {
            if (! is_array($field) || ! preg_match('/^[a-z][a-z0-9_]{0,119}$/', (string) ($field['name'] ?? '')) || isset($names[$field['name']])) {
                throw ValidationException::withMessages(['fields' => 'Field names must be unique lowercase identifiers.']);
            }
            $names[$field['name']] = true;
            if (! $this->types->get((string) ($field['type'] ?? ''))) {
                throw ValidationException::withMessages(['fields' => 'Every field type must be registered.']);
            }
            if (! in_array($field['cardinality'] ?? 'one', ['one', 'many'], true)) {
                throw ValidationException::withMessages(['fields' => 'Field cardinality must be one or many.']);
            }
            if (($field['computed'] ?? false) && ($field['required'] ?? false)) {
                throw ValidationException::withMessages(['fields' => 'Computed fields cannot be required.']);
            }
        }
    }
}

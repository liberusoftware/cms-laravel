<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;

final class ViewDefinitionMutationService
{
    public function create(array $attributes): ViewDefinition
    {
        $this->validateDefinition($attributes);

        return DB::transaction(fn (): ViewDefinition => ViewDefinition::create([
            ...$attributes,
            'definition' => $attributes['definition'] ?? [],
            'visibility' => $attributes['visibility'] ?? 'private',
            'status' => $attributes['status'] ?? 'draft',
        ]));
    }

    public function update(ViewDefinition $view, array $attributes): ViewDefinition
    {
        $merged = [...$view->only(['source', 'definition', 'visibility', 'status']), ...$attributes];
        $this->validateDefinition($merged);
        $view->fill($attributes)->save();

        return $view->refresh();
    }

    private function validateDefinition(array $attributes): void
    {
        if (! is_string($attributes['source'] ?? null) || ! array_key_exists($attributes['source'], config('views-and-query-builder.sources', []))) {
            throw ValidationException::withMessages(['source' => 'Choose a supported view source.']);
        }
        if (! in_array($attributes['visibility'] ?? 'private', config('views-and-query-builder.allowed_visibility', []), true)) {
            throw ValidationException::withMessages(['visibility' => 'Choose a supported visibility.']);
        }
        if (! is_array($attributes['definition'] ?? null)) {
            throw ValidationException::withMessages(['definition' => 'The view definition must be an object.']);
        }

        $definition = $attributes['definition'];
        $fields = array_values(array_filter(array_map(static function (mixed $field): ?string {
            if (is_string($field) && $field !== '') {
                return $field;
            }

            return is_array($field) && is_string($field['name'] ?? null) && $field['name'] !== '' ? $field['name'] : null;
        }, Arr::wrap($definition['fields'] ?? []))));

        foreach (Arr::wrap($definition['filters'] ?? []) as $filter) {
            if (! is_array($filter) || ! in_array($filter['field'] ?? null, $fields, true) || ! in_array(strtolower((string) ($filter['operator'] ?? '=')), config('views-and-query-builder.allowed_operators', []), true)) {
                throw ValidationException::withMessages(['definition' => 'Each filter must use an allowed field and operator.']);
            }
        }

        foreach (Arr::wrap($definition['sorts'] ?? []) as $sort) {
            $field = is_string($sort) ? $sort : ($sort['field'] ?? null);
            $direction = is_string($sort) ? 'asc' : strtolower((string) ($sort['direction'] ?? 'asc'));
            if (! in_array($field, $fields, true) || ! in_array($direction, ['asc', 'desc'], true)) {
                throw ValidationException::withMessages(['definition' => 'Each sort must use an allowed field and direction.']);
            }
        }

        foreach (Arr::wrap($definition['groups'] ?? []) as $field) {
            if (! is_string($field) || ! in_array($field, $fields, true)) {
                throw ValidationException::withMessages(['definition' => 'Each grouping field must be allowed.']);
            }
        }
    }
}

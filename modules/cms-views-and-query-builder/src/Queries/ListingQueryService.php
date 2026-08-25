<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ViewsAndQueryBuilder\Models\ViewDefinition;

final class ListingQueryService
{
    public function execute(ViewDefinition $view, int $perPage = 15, array $input = []): LengthAwarePaginator
    {
        $sources = config('views-and-query-builder.sources', []);
        $modelClass = $sources[$view->source] ?? null;

        if (! is_string($modelClass) || ! class_exists($modelClass)) {
            throw ValidationException::withMessages(['source' => 'The configured view source is unavailable.']);
        }

        $definition = is_array($view->definition) ? $view->definition : [];
        $query = $modelClass::query();
        $allowedFields = $this->allowedFields($definition);

        foreach (Arr::wrap($definition['filters'] ?? []) as $filter) {
            if (! is_array($filter)) {
                throw ValidationException::withMessages(['definition' => 'Each filter must be an object.']);
            }
            $field = (string) ($filter['field'] ?? '');
            $operator = strtolower((string) ($filter['operator'] ?? '='));
            if (! in_array($field, $allowedFields, true) || ! in_array($operator, config('views-and-query-builder.allowed_operators', []), true)) {
                throw ValidationException::withMessages(['definition' => 'The view contains an unsafe filter.']);
            }
            $value = $input[$field] ?? $filter['value'] ?? null;
            $operator === 'in' ? $query->whereIn($field, is_array($value) ? $value : [$value]) : $query->where($field, $operator, $value);
        }

        foreach (Arr::wrap($definition['sorts'] ?? []) as $sort) {
            $field = is_string($sort) ? $sort : ($sort['field'] ?? '');
            $direction = is_string($sort) ? 'asc' : strtolower((string) ($sort['direction'] ?? 'asc'));
            if (! in_array($field, $allowedFields, true) || ! in_array($direction, ['asc', 'desc'], true)) {
                throw ValidationException::withMessages(['definition' => 'The view contains an unsafe sort.']);
            }
            $query->orderBy($field, $direction);
        }

        foreach (Arr::wrap($definition['groups'] ?? []) as $field) {
            if (! is_string($field) || ! in_array($field, $allowedFields, true)) {
                throw ValidationException::withMessages(['definition' => 'The view contains an unsafe grouping field.']);
            }
            $query->groupBy($field);
        }

        return $query->paginate(max(1, min($perPage, (int) config('views-and-query-builder.pagination.max', 100))));
    }

    private function allowedFields(array $definition): array
    {
        $fields = $definition['fields'] ?? [];

        return array_values(array_filter(array_map(static fn ($field): ?string => is_string($field) ? $field : ($field['name'] ?? null), Arr::wrap($fields))));
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder\Actions;

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
    }
}

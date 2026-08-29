<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Taxonomy\Models\Taxonomy;
use Liberu\Cms\Taxonomy\Models\Term;
use Liberu\Cms\Taxonomy\Models\TermAssignment;

final class TaxonomyService
{
    public function create(string $key, string $name, bool $hierarchical = true, bool $exclusive = false, ?int $teamId = null, ?string $description = null): Taxonomy
    {
        $key = trim($key);
        $name = trim($name);
        if ($key === '' || $name === '') {
            throw ValidationException::withMessages(['name' => 'A taxonomy key and name are required.']);
        }

        return Taxonomy::query()->create(['key' => $key, 'name' => $name, 'hierarchical' => $hierarchical, 'exclusive' => $exclusive, 'team_id' => $teamId, 'description' => $description]);
    }

    public function update(Taxonomy $taxonomy, array $attributes): Taxonomy
    {
        foreach (['key', 'name'] as $field) {
            if (array_key_exists($field, $attributes) && trim((string) $attributes[$field]) === '') {
                throw ValidationException::withMessages([$field => 'This field cannot be empty.']);
            }
        }
        if (array_key_exists('hierarchical', $attributes) && ! $attributes['hierarchical'] && $taxonomy->terms()->whereNotNull('parent_id')->exists()) {
            throw ValidationException::withMessages(['hierarchical' => 'A taxonomy with child terms cannot become flat.']);
        }
        $taxonomy->update(array_intersect_key($attributes, array_flip(['key', 'name', 'description', 'hierarchical', 'exclusive'])));

        return $taxonomy->refresh();
    }

    public function delete(Taxonomy $taxonomy): void
    {
        $taxonomy->delete();
    }

    public function addTerm(Taxonomy $taxonomy, string $name, ?string $slug = null, ?int $parentId = null, array $synonyms = [], array $translations = [], ?int $position = null): Term
    {
        $name = trim($name);
        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'A term name is required.']);
        }
        if (! $taxonomy->hierarchical && $parentId !== null) {
            throw ValidationException::withMessages(['parent_id' => 'This vocabulary is not hierarchical.']);
        }
        if ($parentId !== null && ! $taxonomy->terms()->whereKey($parentId)->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'The parent term must belong to this vocabulary.']);
        }
        $slug ??= Str::slug($name);
        if ($slug === '') {
            throw ValidationException::withMessages(['slug' => 'A usable term slug is required.']);
        }

        return $taxonomy->terms()->create(['name' => $name, 'slug' => $slug, 'parent_id' => $parentId, 'synonyms' => array_values($synonyms), 'translations' => $translations, 'position' => $position ?? 0, 'team_id' => $taxonomy->team_id]);
    }

    public function move(Term $term, ?int $parentId, ?int $position = null): Term
    {
        if ($parentId === $term->id || ($parentId !== null && collect($term->descendants())->contains('id', $parentId))) {
            throw ValidationException::withMessages(['parent_id' => 'A term cannot be moved beneath itself or a descendant.']);
        }
        if ($parentId !== null && ! $term->taxonomy->terms()->whereKey($parentId)->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'The parent term must belong to this vocabulary.']);
        }
        $term->update(['parent_id' => $parentId, ...($position === null ? [] : ['position' => $position])]);

        return $term->refresh();
    }

    public function assign(Term $term, string $subjectType, int|string $subjectId): TermAssignment
    {
        if (trim($subjectType) === '' || trim((string) $subjectId) === '') {
            throw ValidationException::withMessages(['subject' => 'Subject type and identifier are required.']);
        }

        if ($term->taxonomy->exclusive) {
            TermAssignment::query()->whereHas('term', fn ($q) => $q->where('taxonomy_id', $term->taxonomy_id))->where('subject_type', $subjectType)->where('subject_id', $subjectId)->delete();
        }

        return TermAssignment::query()->firstOrCreate(['term_id' => $term->id, 'subject_type' => $subjectType, 'subject_id' => $subjectId], ['team_id' => $term->team_id]);
    }

    public function unassign(Term $term, string $subjectType, int|string $subjectId): int
    {
        return TermAssignment::query()->where('term_id', $term->id)->where('subject_type', $subjectType)->where('subject_id', $subjectId)->delete();
    }

    public function merge(Term $from, Term $into): Term
    {
        if ($from->taxonomy_id !== $into->taxonomy_id || $from->id === $into->id) {
            throw ValidationException::withMessages(['term' => 'Terms must be distinct members of the same vocabulary.']);
        }
        DB::transaction(function () use ($from, $into): void {
            TermAssignment::query()->where('term_id', $from->id)->get()->each(fn (TermAssignment $a): TermAssignment => $this->assign($into, $a->subject_type, $a->subject_id));
            $from->children()->update(['parent_id' => $into->id]);
            $from->delete();
        });

        return $into->refresh();
    }

    /** @return array<int, Term> */
    public function terms(Taxonomy $taxonomy, ?string $search = null): array
    {
        return $taxonomy->terms()->withCount('assignments')->when($search, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%")))->orderBy('position')->orderBy('name')->get()->all();
    }
}

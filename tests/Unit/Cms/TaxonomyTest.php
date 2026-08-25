<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Taxonomy\Models\Taxonomy;
use Liberu\Cms\Taxonomy\Services\TaxonomyService;

uses(RefreshDatabase::class);

it('manages hierarchical localized terms and synonyms', function (): void {
    $service = app(TaxonomyService::class);
    $taxonomy = $service->create('topics', 'Topics');
    $parent = $service->addTerm($taxonomy, 'Technology', synonyms: ['Tech'], translations: ['fr' => 'Technologie']);
    $child = $service->addTerm($taxonomy, 'Laravel', parentId: $parent->id);

    expect($child->parent_id)->toBe($parent->id)
        ->and($parent->fresh()->synonyms)->toBe(['Tech'])
        ->and($service->terms($taxonomy, 'lar'))->toHaveCount(1);
});

it('prevents invalid trees and supports exclusive assignments and merges', function (): void {
    $service = app(TaxonomyService::class);
    $taxonomy = $service->create('format', 'Format', exclusive: true);
    $one = $service->addTerm($taxonomy, 'One');
    $two = $service->addTerm($taxonomy, 'Two');

    $service->assign($one, 'post', 7);
    $service->assign($two, 'post', 7);
    expect($one->assignments()->count())->toBe(0)->and($two->assignments()->count())->toBe(1);

    expect(fn () => $service->move($two, $two->id))->toThrow(ValidationException::class);
    $service->merge($two, $one);
    expect($one->fresh()->assignments)->toHaveCount(1)->and(Taxonomy::find($taxonomy->id)->terms)->toHaveCount(1);
});

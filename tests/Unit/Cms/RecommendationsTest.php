<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Recommendations\Models\RecommendationList;
use Liberu\Cms\Recommendations\Services\RecommendationService;

uses(RefreshDatabase::class);

it('supports latest, exclusions, audience context, ranking, and explanations', function (): void {
    $list = RecommendationList::create(['name' => 'Latest', 'key' => 'home-latest', 'kind' => 'latest', 'audience_rules' => ['plan' => ['pro', 'team']], 'exclusions' => ['two']]);
    $list->items()->createMany([
        ['item_type' => 'page', 'item_key' => 'one', 'title' => 'One', 'published_at' => now()->subDay(), 'context' => ['explanation' => 'Fresh content']],
        ['item_type' => 'page', 'item_key' => 'two', 'title' => 'Two', 'published_at' => now()],
    ]);

    $result = app(RecommendationService::class)->recommend('home-latest', ['plan' => 'pro']);

    expect($result)->toHaveCount(1)->and($result[0]['key'])->toBe('one')->and($result[0]['explanation'])->toBe('Fresh content');
});

it('fails closed for an ineligible audience and bounds limits', function (): void {
    $list = RecommendationList::create(['name' => 'Popular', 'key' => 'popular', 'kind' => 'popular', 'audience_rules' => ['country' => 'GB']]);
    $list->items()->create(['item_type' => 'post', 'item_key' => 'one', 'title' => 'One', 'popularity_score' => 20]);

    expect(app(RecommendationService::class)->recommend('popular', ['country' => 'US']))->toBe([])
        ->and(app(RecommendationService::class)->recommend('popular', ['country' => 'GB'], null, 0))->toHaveCount(1);
});

it('creates lists, items, and exclusions through the domain boundary', function (): void {
    $service = app(RecommendationService::class);
    $list = $service->createList('home', 'Home', 'editorial');
    $service->addItem($list, ['item_type' => 'page', 'item_key' => 'one', 'title' => 'One']);
    expect($service->exclude($list, 'one')->exclusions)->toContain('one')
        ->and(fn () => $service->createList('', '', 'invalid'))->toThrow(ValidationException::class);
});

it('updates and archives recommendation lists through the domain boundary', function (): void {
    $service = app(RecommendationService::class);
    $list = $service->createList('home', 'Home');

    expect($service->updateList($list, ['name' => 'Homepage', 'kind' => 'trending'])->name)->toBe('Homepage');
    expect($service->removeList($list->fresh())->active)->toBeFalse();
});

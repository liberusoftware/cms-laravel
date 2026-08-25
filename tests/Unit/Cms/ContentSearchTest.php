<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Contracts\Search\SearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Search\Services\ContentSearchService;

uses(RefreshDatabase::class);

it('searches registered sources and records analytics', function (): void {
    app(SearchRegistryInterface::class)->registerSource(new class implements SearchableSourceInterface
    {
        public function search(string $query): iterable
        {
            return [new SearchResult('page', 42, 'Home', 'home', 'Welcome', 0.9)];
        }
    });

    $results = app(ContentSearchService::class)->search('home', 3);

    expect($results)->toHaveCount(1)
        ->and(app(ContentSearchService::class)->analytics(3)->total())->toBe(1);
});

it('rejects short queries and provides autocomplete from analytics', function (): void {
    $service = app(ContentSearchService::class);
    expect(fn () => $service->search('x', 3))->toThrow(ValidationException::class);

    $service->search('homepage', 3);
    $service->search('home news', 3);
    expect($service->autocomplete('home', 3))->toContain('homepage', 'home news');
});

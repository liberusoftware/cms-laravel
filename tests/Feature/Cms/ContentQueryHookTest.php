<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Hooks\Filters\ContentQueryFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Core\Tenant\TenantScope;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

function publishedPage(array $attributes = []): Page
{
    return Page::factory()->create([
        'status' => WorkflowState::Published->value,
        'published_at' => now()->subDay(),
        ...$attributes,
    ]);
}

it('lets an extension narrow a content query via ContentQueryFilter', function (): void {
    publishedPage(['title' => 'Keep']);
    publishedPage(['title' => 'Drop']);

    app(HookBusInterface::class)->listen(ContentQueryFilter::class, function (ContentQueryFilter $filter): void {
        if ($filter->key === 'pages.published') {
            $filter->query->where('title', 'Keep');
        }
    });

    $titles = collect(app(PageRepositoryInterface::class)->published())->pluck('title')->all();

    expect($titles)->toBe(['Keep']);
});

it('carries the query identity key so a callback can target one query', function (): void {
    $keys = [];

    app(HookBusInterface::class)->listen(ContentQueryFilter::class, function (ContentQueryFilter $filter) use (&$keys): void {
        $keys[] = $filter->key;
    });

    app(PageRepositoryInterface::class)->roots();

    expect($keys)->toBe(['pages.roots']);
});

it('prevents a ContentQueryFilter callback from leaking other tenants rows', function (): void {
    $teamA = Team::factory()->create();
    $teamB = Team::factory()->create();

    $context = app(TenantContextInterface::class);
    $context->setTenantId($teamA->id);

    $mine = publishedPage(['team_id' => $teamA->id]);
    $theirs = publishedPage(['team_id' => $teamB->id]);

    // A careless callback drops the tenant global scope; the repository must
    // re-assert isolation regardless.
    app(HookBusInterface::class)->listen(ContentQueryFilter::class, function (ContentQueryFilter $filter): void {
        $filter->query->withoutGlobalScope(TenantScope::class);
    });

    $context->setTenantId($teamA->id);
    $ids = collect(app(PageRepositoryInterface::class)->published())->pluck('id')->all();

    expect($ids)->toContain($mine->id)
        ->and($ids)->not->toContain($theirs->id);
});

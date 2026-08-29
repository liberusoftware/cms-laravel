<?php

declare(strict_types=1);

use Liberu\Cms\AnalyticsIntegrationFilament\Resources\AnalyticsEventResource as FilamentAnalyticsEventResource;
use Liberu\Cms\AnalyticsIntegrationLivewire\Livewire\AnalyticsDashboard;
use Livewire\Livewire;

it('registers analytics presentation surfaces with stable identities', function (): void {
    expect(FilamentAnalyticsEventResource::getSlug())->toBe('analytics-events')
        ->and(app('livewire')->exists('module-cms-analytics-integration.dashboard'))->toBeTrue();
});

it('renders the analytics dashboard with domain-owned state', function (): void {
    Livewire::test(AnalyticsDashboard::class)->assertSee('Total events: 0');
});

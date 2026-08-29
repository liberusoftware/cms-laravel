<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\RegionsAndWidgets\Services\RegionWidgetService;

uses(RefreshDatabase::class);

it('places, orders, filters, and caches widgets by context', function (): void {
    $service = app(RegionWidgetService::class);
    $region = $service->createRegion('Homepage Main', 'Homepage Main');
    $visible = $service->createWidget('welcome', 'text', ['body' => 'Welcome']);
    $member = $service->createWidget('member-offer', 'promo');
    $service->place($region, $member, 2, ['audience' => 'member']);
    $service->place($region, $visible, 1);

    expect($service->render('homepage-main', ['audience' => 'guest']))->toHaveCount(1)
        ->and($service->render('homepage-main', ['audience' => 'member']))->toHaveCount(2)
        ->and($service->render('homepage-main', ['audience' => 'member'])[0]['key'])->toBe('welcome');
});

it('rejects invalid placement schedules', function (): void {
    $service = app(RegionWidgetService::class);
    $region = $service->createRegion('sidebar', 'Sidebar');
    $widget = $service->createWidget('notice', 'text');

    expect(fn () => $service->place($region, $widget, 0, [], '2026-01-02', '2026-01-01'))->toThrow(ValidationException::class);
});

it('requires region and widget identities at the domain boundary', function (): void {
    $service = app(RegionWidgetService::class);
    expect(fn () => $service->createRegion('', ''))->toThrow(ValidationException::class)
        ->and(fn () => $service->createWidget('', ''))->toThrow(ValidationException::class);
});

it('rejects cross-team widget placements', function (): void {
    $service = app(RegionWidgetService::class);
    $region = $service->createRegion('team-a-sidebar', 'Sidebar', teamId: 10);
    $widget = $service->createWidget('team-b-notice', 'text', teamId: 11);

    expect(fn () => $service->place($region, $widget))->toThrow(ValidationException::class);
});

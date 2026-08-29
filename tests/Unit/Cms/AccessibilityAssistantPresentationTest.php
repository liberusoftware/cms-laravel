<?php

declare(strict_types=1);

use Liberu\Cms\AccessibilityAssistantFilament\Resources\AccessibilityResource;
use Liberu\Cms\AccessibilityAssistantLivewire\Livewire\AccessibilityAnalyzer;
use Livewire\Livewire;

it('registers the Filament resource and Livewire analyzer surface', function (): void {
    expect(AccessibilityResource::getSlug())->toBe('accessibility-assistant')
        ->and(app('livewire')->exists('module-cms-accessibility-assistant.analyzer'))->toBeTrue();
});

it('delegates accessibility analysis from Livewire state to the domain service', function (): void {
    Livewire::test(AccessibilityAnalyzer::class)
        ->set('html', '<img src="hero.jpg">')
        ->assertSet('findings.0.code', 'image-alt');
});

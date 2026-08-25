<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;

uses(RefreshDatabase::class);

it('exposes only security-approved marketplace themes', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read'], 'sanctum');
    MarketplaceTheme::create(['key' => 'pending', 'name' => 'Pending', 'version' => '1.0.0', 'author' => 'Test', 'license' => 'MIT', 'manifest' => [], 'status' => 'published', 'security_status' => 'pending', 'team_id' => $team->id]);
    MarketplaceTheme::create(['key' => 'approved', 'name' => 'Approved', 'version' => '1.0.0', 'author' => 'Test', 'license' => 'MIT', 'manifest' => [], 'status' => 'published', 'security_status' => 'approved', 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/theme-marketplace')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.key', 'approved');
});

it('exposes approved theme details, installation, and ratings through domain actions', function (): void {
    $team = Team::factory()->create();
    Sanctum::actingAs($team, ['content:read', 'content:write'], 'sanctum');
    MarketplaceTheme::create(['key' => 'directory', 'name' => 'Directory', 'version' => '1.0.0', 'author' => 'Test', 'license' => 'MIT', 'manifest' => [], 'compatibility' => ['cms' => '1.0.0'], 'status' => 'published', 'security_status' => 'approved', 'team_id' => $team->id]);

    $this->getJson('/api/v1/cms/theme-marketplace/directory')
        ->assertOk()
        ->assertJsonPath('data.type', 'cms-marketplace-theme');

    $this->postJson('/api/v1/cms/theme-marketplace/directory/install', ['site_key' => 'main', 'cms_version' => '1.2.0'])
        ->assertCreated()
        ->assertJsonPath('data.type', 'cms-theme-installation');

    $this->postJson('/api/v1/cms/theme-marketplace/directory/rate', ['reviewer_type' => 'team', 'reviewer_id' => (string) $team->id, 'rating' => 5])
        ->assertOk()
        ->assertJsonPath('data.rating.count', 1);
});

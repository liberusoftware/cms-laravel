<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Audit\Models\AuditLog;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->team = Team::factory()->create();
});

it('requires the audit read scope', function (): void {
    Sanctum::actingAs($this->team, ['content:read'], 'sanctum');

    $this->getJson('/api/v1/cms/audit-and-history')->assertForbidden();
});

it('lists and retrieves immutable audit records through the API', function (): void {
    Sanctum::actingAs($this->team, ['audit:read'], 'sanctum');
    $log = AuditLog::query()->create(['action' => 'content.published', 'subject_type' => 'page', 'subject_id' => '42']);

    $this->getJson('/api/v1/cms/audit-and-history')
        ->assertOk()
        ->assertJsonPath('data.0.id', (string) $log->id)
        ->assertJsonPath('data.0.type', 'cms-audit-and-history');

    $this->getJson('/api/v1/cms/audit-and-history/'.$log->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.action', 'content.published');
});

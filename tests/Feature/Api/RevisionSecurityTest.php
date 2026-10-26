<?php

declare(strict_types=1);

use App\Models\Team;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Cms\Pages\Models\Page;
use Liberu\Cms\Revisions\Services\RevisionService;

uses(RefreshDatabase::class);

it('scopes revision reads and mutations to the subjects tenant', function (): void {
    $morphs = Relation::morphMap();
    Relation::morphMap(['security-page' => Page::class]);
    try {
        $team = Team::factory()->create();
        $other = Team::factory()->create();
        $ownPage = Page::create(['title' => 'Own page', 'slug' => 'own-page', 'team_id' => $team->id]);
        $foreignPage = Page::create(['title' => 'Private page', 'slug' => 'private-page', 'team_id' => $other->id]);
        $service = app(RevisionService::class);
        $own = $service->create('security-page', $ownPage->id, ['title' => 'Own draft']);
        $foreign = $service->create('security-page', $foreignPage->id, ['title' => 'Private draft']);
        Sanctum::actingAs($team, ['content:read', 'content:write', 'content:publish'], 'sanctum');

        $this->getJson('/api/v1/cms/revisions/security-page/'.$ownPage->id.'?per_page=1000000')->assertOk()->assertJsonPath('data.per_page', 100);
        $this->getJson('/api/v1/cms/revisions/security-page/'.$foreignPage->id)->assertNotFound();
        $this->getJson('/api/v1/cms/revisions/'.$own->id.'/compare/'.$foreign->id)->assertNotFound();
        foreach (['restore', 'branch', 'publish'] as $action) {
            $this->postJson('/api/v1/cms/revisions/'.$foreign->id.'/'.$action, ['branch' => 'review'])->assertNotFound();
        }
        foreach (['', '/autosave', '/prune'] as $action) {
            $this->postJson('/api/v1/cms/revisions/security-page/'.$foreignPage->id.$action, ['snapshot' => ['title' => 'Injected']])->assertNotFound();
        }
        $this->getJson('/api/v1/cms/revisions/unknown/'.$ownPage->id)->assertNotFound();
        expect($foreign->fresh()->snapshot())->toBe(['title' => 'Private draft']);
        $this->postJson('/api/v1/cms/revisions/'.$own->id.'/restore')->assertCreated();
        $this->postJson('/api/v1/cms/revisions/'.$own->id.'/branch', ['branch' => 'review'])->assertCreated();
        $this->postJson('/api/v1/cms/revisions/'.$own->id.'/publish')->assertOk();
        $this->getJson('/api/v1/cms/revisions/'.$own->id.'/compare/'.$own->id)->assertOk();

        Sanctum::actingAs($team, ['content:read'], 'sanctum');
        $this->getJson('/api/v1/cms/revisions/security-page/'.$ownPage->id)->assertForbidden();
        $this->postJson('/api/v1/cms/revisions/'.$own->id.'/restore')->assertForbidden();
        $this->getJson('/api/v1/cms/revisions/'.$own->id.'/compare/'.$own->id)->assertForbidden();
    } finally {
        Relation::morphMap($morphs, false);
    }
});

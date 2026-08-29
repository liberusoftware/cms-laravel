<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('analyzes content through the accessibility assistant API', function (): void {
    $this->postJson('/api/v1/cms/accessibility-assistant/analyze', ['html' => '<img src="hero.jpg">'])
        ->assertOk()
        ->assertJsonPath('data.findings.0.code', 'image-alt');
});

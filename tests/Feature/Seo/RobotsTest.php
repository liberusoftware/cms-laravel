<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves robots.txt with crawl rules and the sitemap location', function (): void {
    $response = $this->get('/robots.txt');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('text/plain');

    $response->assertSee('User-agent: *', false)
        ->assertSee('Disallow: /admin', false)
        ->assertSee('Sitemap: '.url('/sitemap.xml'), false);
});

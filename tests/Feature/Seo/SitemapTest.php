<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

it('lists published pages in the sitemap and excludes drafts', function (): void {
    Page::factory()->published()->create(['slug' => 'about', 'title' => 'About']);
    Page::factory()->create(['slug' => 'secret-draft']);

    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/xml');

    $response->assertSee(url('/about'), false)
        ->assertDontSee(url('/secret-draft'), false);
});

it('lists the home page at the site root, not /home', function (): void {
    Page::factory()->published()->home()->create();

    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertSee('<loc>'.url('/').'</loc>', false)
        ->assertDontSee(url('/home'), false);
});

it('renders a well-formed urlset', function (): void {
    Page::factory()->published()->create(['slug' => 'one']);

    $body = $this->get('/sitemap.xml')->getContent();

    expect($body)
        ->toContain('<?xml version="1.0" encoding="UTF-8"?>')
        ->toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">')
        ->toContain('</urlset>');
});

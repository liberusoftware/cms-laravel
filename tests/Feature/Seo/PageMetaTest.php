<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

it('renders SEO head tags for a published page', function (): void {
    Page::factory()->published()->create([
        'slug' => 'about-us',
        'title' => 'About Us',
        'excerpt' => 'Everything about our company.',
        'template' => 'default',
    ]);

    $response = $this->get('/about-us');

    $response->assertOk()
        ->assertSee('<meta name="description" content="Everything about our company."', false)
        ->assertSee('<meta property="og:title" content="About Us"', false)
        ->assertSee('<link rel="canonical" href="'.url('/about-us').'"', false)
        ->assertSee('application/ld+json', false);
});

it('escapes author-supplied text in meta tags and JSON-LD', function (): void {
    Page::factory()->published()->create([
        'slug' => 'xss',
        'title' => 'Hi "<script>alert(1)</script>"',
        'excerpt' => 'safe excerpt',
        'template' => 'default',
    ]);

    $response = $this->get('/xss');

    $response->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false);
});

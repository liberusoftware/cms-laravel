<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\CollectionsLivewire\Livewire\CollectionBrowser;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('stores a typed collection schema and structured record data', function (): void {
    $collection = Collection::create([
        'name' => 'FAQs',
        'slug' => 'faqs',
        'type' => 'faq',
        'schema' => ['question' => ['type' => 'text'], 'answer' => ['type' => 'rich-text']],
    ]);
    $item = $collection->items()->create([
        'title' => 'How does it work?',
        'data' => ['question' => 'How does it work?', 'answer' => 'Very well.'],
        'status' => 'published',
        'published_at' => now(),
    ]);

    expect($collection->fresh()->schema)->toHaveKey('question')
        ->and($item->fresh()->data['answer'])->toBe('Very well.')
        ->and($item->fresh()->excerpt)->toBeNull();
});

it('generates an excerpt for text content and identifies live records', function (): void {
    $item = Collection::create(['name' => 'Testimonials', 'slug' => 'testimonials'])
        ->items()->create([
            'title' => 'A testimonial',
            'content' => 'This is a short testimonial body.',
            'status' => 'published',
            'published_at' => Carbon::now()->subMinute(),
        ]);

    expect($item->fresh()->excerpt)->toContain('short testimonial')
        ->and($item->fresh()->isLive())->toBeTrue();
});

it('browses only published records through the Livewire adapter', function (): void {
    $collection = Collection::create(['name' => 'Directory', 'slug' => 'directory', 'type' => 'directory']);
    $visible = $collection->items()->create(['title' => 'Visible record', 'status' => 'published', 'published_at' => now()]);
    $collection->items()->create(['title' => 'Draft record', 'status' => 'draft']);

    Livewire::test(CollectionBrowser::class, ['collection' => $collection->slug])
        ->assertSee($visible->title)
        ->assertDontSee('Draft record');
});

<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\EditorialContent\Services\EditorialContentService;

uses(RefreshDatabase::class);

it('manages editorial posts, authors, series, featuring, and archives', function (): void {
    $service = app(EditorialContentService::class);
    $author = $service->author('Ada Lovelace', 7, 'Mathematician');
    $series = $service->series('CMS History', 7);
    $post = $service->post(['slug' => 'first-post', 'title' => 'First Post', 'excerpt' => 'Intro', 'status' => 'draft', 'featured' => true, 'author_id' => $author->id, 'series_id' => $series->id, 'tags' => ['cms', 'history']], 7);
    $published = $service->publish($post);
    $archived = $service->archive($published);

    expect($author->name)->toBe('Ada Lovelace')
        ->and($series->name)->toBe('CMS History')
        ->and($archived->status)->toBe('archived')
        ->and($archived->archived_at)->not->toBeNull();
});

it('rejects invalid status and duplicate tenant slugs', function (): void {
    $service = app(EditorialContentService::class);
    $service->post(['slug' => 'same', 'title' => 'One'], 7);

    expect(fn () => $service->post(['slug' => 'same', 'title' => 'Two'], 7))->toThrow(QueryException::class);
    expect(fn () => $service->post(['slug' => 'bad', 'title' => 'Bad', 'status' => 'unknown'], 7))->toThrow(ValidationException::class);
});

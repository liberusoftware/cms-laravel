<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\KnowledgeBase\Services\KnowledgeBaseService;

uses(RefreshDatabase::class);

it('supports tenant-scoped article hierarchies, versions, publishing, feedback, and related content', function (): void {
    $service = app(KnowledgeBaseService::class);
    $parent = $service->create('getting-started', 'Getting started', 'Welcome', 7);
    $article = $service->create('install', 'Install', 'Install steps', 7, $parent->id);
    $version = $service->version($article, 'Updated install steps', 'editor-1');
    $service->publish($article, 7);
    $service->feedback($article, true, 'Solved my issue', 'reader-1', 7);

    expect($version->version)->toBe(1)
        ->and($article->refresh()->status)->toBe('published')
        ->and($service->related($parent, 7))->toHaveCount(1);
});

it('rejects invalid parents and cross-tenant mutations', function (): void {
    $service = app(KnowledgeBaseService::class);
    $article = $service->create('article', 'Article', 'Body', 7);

    expect(fn () => $service->create('child', 'Child', 'Body', 8, $article->id))->toThrow(ValidationException::class)
        ->and(fn () => $service->publish($article, 8))->toThrow(ValidationException::class);
});

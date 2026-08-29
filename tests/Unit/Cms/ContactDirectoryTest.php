<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContactDirectory\Services\ContactDirectoryService;

uses(RefreshDatabase::class);

it('creates validated contacts and scopes public directory reads', function (): void {
    $service = app(ContactDirectoryService::class);
    $public = $service->saveContact(['name' => 'Ada Lovelace', 'email' => 'ada@example.test', 'is_public' => true], 3);
    $service->saveContact(['name' => 'Private Contact', 'email' => 'private@example.test', 'is_public' => false], 3);

    expect($public->team_id)->toBe(3)
        ->and($service->contacts(3, true)->total())->toBe(1);
});

it('rejects invalid contacts and supports directory metadata', function (): void {
    $service = app(ContactDirectoryService::class);

    expect(fn () => $service->saveContact(['name' => 'Bad', 'email' => 'invalid'], 3))
        ->toThrow(ValidationException::class);

    expect($service->category(['name' => 'Leadership'], 3)->slug)->toBe('leadership')
        ->and($service->location(['name' => 'HQ', 'city' => 'London'], 3)->city)->toBe('London')
        ->and($service->form(['name' => 'Contact us', 'schema' => [['name' => 'message']]], 3)->is_active)->toBeTrue();
});

it('rejects contact metadata from another tenant', function (): void {
    $service = app(ContactDirectoryService::class);
    $category = $service->category(['name' => 'External'], 4);

    expect(fn () => $service->saveContact(['name' => 'Invalid', 'category_id' => $category->id], 3))
        ->toThrow(ValidationException::class);
});

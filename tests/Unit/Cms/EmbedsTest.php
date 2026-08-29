<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Embeds\Services\EmbedsService;

uses(RefreshDatabase::class);
it('validates provider URLs and gates consent embeds', function (): void {
    $s = app(EmbedsService::class);
    $p = $s->provider(['key' => 'youtube', 'name' => 'YouTube', 'domain_pattern' => 'youtube.com']);
    $e = $s->embed(['provider_id' => $p->id, 'external_key' => 'abc', 'url' => 'https://youtube.com/watch?v=abc', 'title' => 'Video', 'privacy_mode' => 'consent', 'fallback_url' => 'https://example.com/fallback']);
    $s->publish($e);
    expect($s->render($e)['status'])->toBe('fallback')->and($s->render($e, true)['status'])->toBe('published');
    expect(fn () => $s->embed(['provider_id' => $p->id, 'external_key' => 'bad', 'url' => 'https://evil.example/video']))->toThrow(ValidationException::class);
});

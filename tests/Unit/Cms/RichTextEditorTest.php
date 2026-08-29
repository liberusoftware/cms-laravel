<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Liberu\Cms\RichTextEditor\Services\RichTextService;

it('cleans, sanitizes, and reports rich text accessibility hints', function (): void {
    $result = app(RichTextService::class)->prepare('<p style="color:red">Hello</p><img src="x"><script>alert(1)</script>');
    expect($result['html'])->not->toContain('<script')->not->toContain('style=')->and($result['accessibility'])->toContain('Images should have alternative text.');
});
it('validates embeds and formats', function (): void {
    $service = app(RichTextService::class);
    expect($service->embed('https://example.com'))->toContain('data-embed')->and(fn () => $service->prepare('x', 'invalid'))->toThrow(ValidationException::class)->and(fn () => $service->embed('javascript:alert(1)'))->toThrow(ValidationException::class);
});

it('sanitizes direct input and generates escaped embed titles', function (): void {
    $service = app(RichTextService::class);
    expect($service->sanitize('<script>x</script><p>Safe</p>'))->not->toContain('<script')
        ->and($service->embed('https://example.com/video', '<Video>'))->toContain('&lt;Video&gt;');
});

it('rejects credentialed and private-network embed targets', function (): void {
    $service = app(RichTextService::class);

    expect(fn () => $service->embed('https://user:secret@example.com/video'))->toThrow(ValidationException::class)
        ->and(fn () => $service->embed('http://127.0.0.1/video'))->toThrow(ValidationException::class)
        ->and(fn () => $service->embed('http://localhost/video'))->toThrow(ValidationException::class);
});

<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditor\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Content\Support\HtmlSanitizer;

final class RichTextService
{
    public function __construct(private readonly HtmlSanitizer $sanitizer) {}

    public function sanitize(?string $html): string
    {
        return $this->sanitizer->sanitize($this->cleanPaste($html ?? ''));
    }

    public function cleanPaste(string $html): string
    {
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;
        $html = preg_replace('/<span[^>]*>(.*?)<\/span>/is', '$1', $html) ?? $html;
        $html = preg_replace('/\sstyle=("|\').*?\1/i', '', $html) ?? $html;

        return trim($html);
    }

    /** @return array{html:string, warnings:array<int,string>, accessibility:array<int,string>} */
    public function prepare(string $html, string $format = 'html'): array
    {
        if (! in_array($format, ['html', 'markdown', 'plain'], true)) {
            throw ValidationException::withMessages(['format' => 'Unsupported rich text format.']);
        }
        if ($format === 'plain') {
            return ['html' => e(strip_tags($html)), 'warnings' => [], 'accessibility' => []];
        }
        $clean = $this->sanitize($html);
        $warnings = [];
        if (preg_match('/<a\b(?![^>]*\brel=)/i', $clean)) {
            $warnings[] = 'External links should include a rel attribute.';
        }
        $accessibility = $this->accessibilityHints($clean);

        return ['html' => $clean, 'warnings' => $warnings, 'accessibility' => $accessibility];
    }

    /** @return array<int,string> */
    public function accessibilityHints(string $html): array
    {
        $hints = [];
        if (preg_match('/<img\b(?![^>]*\balt=)/i', $html)) {
            $hints[] = 'Images should have alternative text.';
        }
        if (preg_match('/<table\b(?![^>]*\b aria-label=)(?![^>]*\bcaption)/i', $html)) {
            $hints[] = 'Tables should have a caption or accessible label.';
        }
        if (preg_match('/<h[1-6][^>]*>\s*<\/h[1-6]>/i', $html)) {
            $hints[] = 'Empty headings should be removed.';
        }

        return $hints;
    }

    public function embed(string $url, ?string $title = null): string
    {
        if (! Str::startsWith($url, ['https://', 'http://'])) {
            throw ValidationException::withMessages(['url' => 'Embeds require an HTTP or HTTPS URL.']);
        }

        return '<figure data-embed="'.e($url).'" role="group"><a href="'.e($url).'" rel="noopener noreferrer">'.e($title ?? $url).'</a></figure>';
    }
}

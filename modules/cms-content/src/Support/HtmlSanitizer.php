<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer as SymfonyHtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Sanitises author-supplied HTML before it is rendered. Keeps safe formatting
 * (headings, links, lists, images, tables, …) and strips scripts, inline event
 * handlers, and dangerous URL schemes — the fix for stored XSS (OWASP A03) when
 * content is echoed as raw HTML.
 */
final class HtmlSanitizer
{
    private readonly SymfonyHtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowLinkSchemes(['https', 'http', 'mailto'])
            ->allowRelativeLinks()
            ->allowRelativeMedias();

        $this->sanitizer = new SymfonyHtmlSanitizer($config);
    }

    public function sanitize(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        return $this->sanitizer->sanitize($html);
    }
}

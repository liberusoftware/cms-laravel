<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistant\Services;

use Illuminate\Validation\ValidationException;

final class AccessibilityAssistantService
{
    /**
     * @param  array<int, string>  $exceptions
     * @return array<int, array{code:string, severity:string, message:string}>
     */
    public function analyze(string $html, array $exceptions = []): array
    {
        if (trim($html) === '') {
            throw ValidationException::withMessages(['html' => 'Content to analyze is required.']);
        }

        $findings = [];
        $this->finding($findings, 'image-alt', 'error', 'Images should have alternative text.', preg_match('/<img\\b(?![^>]*\\balt\\s*=)/i', $html) === 1, $exceptions);
        $this->finding($findings, 'table-caption', 'warning', 'Tables should have a caption or accessible label.', preg_match('/<table\\b(?![^>]*\\baria-label\\s*=)(?![^>]*<caption\\b)/i', $html) === 1, $exceptions);
        $this->finding($findings, 'empty-heading', 'warning', 'Empty headings should be removed.', preg_match('/<h[1-6][^>]*>\\s*<\\/h[1-6]>/i', $html) === 1, $exceptions);
        $this->finding($findings, 'link-name', 'error', 'Links should have discernible text or an accessible label.', preg_match('/<a\\b(?![^>]*(?:aria-label|title)\\s*=)[^>]*>\\s*<\\/a>/i', $html) === 1, $exceptions);
        $this->finding($findings, 'document-language', 'error', 'The document should declare a language.', preg_match('/<html\\b(?![^>]*\\blang\\s*=)/i', $html) === 1, $exceptions);
        $this->finding($findings, 'video-captions', 'warning', 'Videos should provide captions.', preg_match('/<video\\b(?![\\s\\S]*<track\\b[^>]*kind\\s*=\\s*["\\\']captions)/i', $html) === 1, $exceptions);

        return $findings;
    }

    /**
     * @param  array<int, array{code:string, severity:string, message:string}>  $findings
     * @param  array<int, string>  $exceptions
     */
    private function finding(array &$findings, string $code, string $severity, string $message, bool $failed, array $exceptions): void
    {
        if ($failed && ! in_array($code, $exceptions, true)) {
            $findings[] = ['code' => $code, 'severity' => $severity, 'message' => $message];
        }
    }
}

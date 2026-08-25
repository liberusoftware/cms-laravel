<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant\Contracts;

interface TranslationProviderInterface
{
    /** @return array{translation: string, confidence: float, provider: string, model: string, provenance: array<string, mixed>} */
    public function translate(string $text, string $sourceLocale, string $targetLocale, array $context = []): array;
}

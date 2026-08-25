<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Contracts;

use Liberu\Cms\TranslationManagement\Support\TranslationResult;

interface TranslationVendorInterface
{
    public function key(): string;

    public function translate(string $source, string $sourceLocale, string $targetLocale, array $context = []): TranslationResult;
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Support;

final readonly class TranslationResult
{
    public function __construct(
        public string $text,
        public string $provider,
        public string $model,
        public float $cost = 0.0,
        public array $provenance = [],
    ) {}
}

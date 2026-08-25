<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Support;

final readonly class TranscodeResult
{
    public function __construct(public string $uri, public ?int $bytes = null, public array $metadata = []) {}
}

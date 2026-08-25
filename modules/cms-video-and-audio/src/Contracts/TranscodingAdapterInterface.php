<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Contracts;

use Liberu\Cms\VideoAndAudio\Support\TranscodeResult;

interface TranscodingAdapterInterface
{
    public function key(): string;

    public function transcode(string $sourceUri, string $profile, array $context = []): TranscodeResult;
}

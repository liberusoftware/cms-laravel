<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Support;

final readonly class PlaybackMetadata
{
    public function __construct(public string $publicId, public string $kind, public string $title, public ?string $streamUri, public ?string $posterUri, public ?int $durationSeconds, public array $tracks, public array $metadata) {}
}

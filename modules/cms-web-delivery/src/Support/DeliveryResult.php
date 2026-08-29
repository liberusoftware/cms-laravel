<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDelivery\Support;

final readonly class DeliveryResult
{
    public function __construct(
        public int $status,
        public string $path,
        public ?string $body,
        /** @var array<string, mixed> */
        public array $metadata,
        /** @var array<int, string> */
        public array $cacheTags,
        public ?string $canonicalUrl = null,
        public ?string $redirectUrl = null,
        public bool $preview = false,
    ) {}
}

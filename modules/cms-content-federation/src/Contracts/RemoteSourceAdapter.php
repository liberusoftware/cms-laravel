<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation\Contracts;

interface RemoteSourceAdapter
{
    /** @return array{payload: array<string, mixed>, etag?: string|null} */
    public function fetch(string $externalType, string $externalKey, array $configuration = []): array;
}

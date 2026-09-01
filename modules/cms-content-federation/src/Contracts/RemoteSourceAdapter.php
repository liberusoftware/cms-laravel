<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation\Contracts;

interface RemoteSourceAdapter
{
    /**
     * @param  array<string, mixed>  $configuration
     * @return array{payload: array<string, mixed>, etag?: string|null}
     */
    public function fetch(string $externalType, string $externalKey, array $configuration = []): array;
}

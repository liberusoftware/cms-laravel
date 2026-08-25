<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio\Support;

use InvalidArgumentException;
use Liberu\Cms\VideoAndAudio\Contracts\TranscodingAdapterInterface;

final class TranscodingAdapterRegistry
{
    /** @var array<string, TranscodingAdapterInterface> */
    private array $adapters = [];

    public function register(TranscodingAdapterInterface $adapter): void
    {
        $this->adapters[$adapter->key()] = $adapter;
    }

    public function resolve(string $key): TranscodingAdapterInterface
    {
        if (! isset($this->adapters[$key])) {
            throw new InvalidArgumentException("Transcoding adapter [{$key}] is not registered.");
        }

        return $this->adapters[$key];
    }
}

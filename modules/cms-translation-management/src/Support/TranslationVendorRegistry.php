<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Support;

use InvalidArgumentException;
use Liberu\Cms\TranslationManagement\Contracts\TranslationVendorInterface;

final class TranslationVendorRegistry
{
    /** @var array<string, TranslationVendorInterface> */
    private array $vendors = [];

    public function register(TranslationVendorInterface $vendor): void
    {
        $this->vendors[$vendor->key()] = $vendor;
    }

    public function resolve(string $key): TranslationVendorInterface
    {
        if (! isset($this->vendors[$key])) {
            throw new InvalidArgumentException("Translation vendor [{$key}] is not registered.");
        }

        return $this->vendors[$key];
    }
}

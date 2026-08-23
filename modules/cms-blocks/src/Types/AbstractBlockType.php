<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

use Liberu\Cms\Contracts\Block\BlockTypeInterface;

/**
 * Base for block types with small helpers for safely reading and escaping data.
 * All output is HTML-escaped by default — blocks never emit untrusted markup.
 */
abstract class AbstractBlockType implements BlockTypeInterface
{
    /**
     * @param  array<array-key, mixed>  $data
     */
    protected function str(array $data, string $key, string $default = ''): string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : $default;
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    protected function int(array $data, string $key, int $default = 0): int
    {
        $value = $data[$key] ?? null;

        return is_int($value) ? $value : $default;
    }

    protected function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }
}

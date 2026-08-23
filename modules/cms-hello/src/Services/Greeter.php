<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Services;

use Liberu\Cms\Hello\Contracts\GreeterInterface;

final readonly class Greeter implements GreeterInterface
{
    public function __construct(private string $template) {}

    public function greet(string $name): string
    {
        return str_replace(':name', $name, $this->template);
    }
}

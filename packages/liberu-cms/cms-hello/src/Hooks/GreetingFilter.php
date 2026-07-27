<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Hooks;

use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * cms-hello's own filter point: lets another extension transform a greeting
 * message before it is rendered. Demonstrates that an extension can itself be
 * extensible — hello applies this filter, and anyone may listen on it.
 */
final class GreetingFilter implements Filter
{
    public function __construct(public string $message) {}

    public function name(): string
    {
        return 'hello.greeting';
    }
}

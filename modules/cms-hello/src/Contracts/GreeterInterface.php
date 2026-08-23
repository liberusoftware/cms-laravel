<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Contracts;

/**
 * The Hello module's own public contract.
 *
 * Bound in the container by the module's provider. Consumers resolve this
 * interface rather than the concrete Greeter, demonstrating the pattern every
 * module follows to expose capability without leaking implementation.
 */
interface GreeterInterface
{
    public function greet(string $name): string;
}

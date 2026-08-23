<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Hello\Contracts\GreeterInterface;
use Liberu\Cms\Hello\Events\HelloGreeted;
use Liberu\Cms\Hello\Models\Greeting;

/**
 * Thin controller: it delegates the greeting to the module's service, records
 * it, and announces the result on the event bus. No business logic lives here.
 */
final readonly class HelloController
{
    public function __construct(
        private GreeterInterface $greeter,
        private EventBusInterface $events,
    ) {}

    public function __invoke(string $name = 'world'): JsonResponse
    {
        $message = $this->greeter->greet($name);

        Greeting::create(['name' => $name, 'message' => $message]);

        $this->events->dispatch(new HelloGreeted($name, $message));

        return new JsonResponse([
            'module' => 'hello',
            'message' => $message,
        ]);
    }
}

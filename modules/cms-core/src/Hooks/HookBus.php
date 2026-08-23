<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Hooks;

use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Liberu\Cms\Contracts\Hooks\Filter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;

/**
 * In-memory implementation of the hook pipeline.
 *
 * Registrations are held on this singleton (unlike the EventBus, there is no
 * framework primitive to delegate to), keyed by the concrete Filter class.
 * {@see apply()} sorts a copy of the registrations by ascending priority — ties
 * broken by registration order so the pipeline is deterministic — then invokes
 * each callback with the filter, discarding return values; callbacks mutate the
 * payload in place. Class/array callbacks are resolved through the container so
 * their own dependencies are injected.
 */
final class HookBus implements HookBusInterface
{
    /**
     * @var array<class-string<Filter>, list<array{priority: int, order: int, callback: Closure|class-string|array{0: class-string, 1: string}}>>
     */
    private array $callbacks = [];

    private int $sequence = 0;

    public function __construct(private readonly Container $container) {}

    public function listen(string $filterClass, Closure|string|array $callback, int $priority = 0): void
    {
        $this->callbacks[$filterClass][] = [
            'priority' => $priority,
            'order' => $this->sequence++,
            'callback' => $callback,
        ];
    }

    public function apply(Filter $filter): Filter
    {
        $registered = $this->callbacks[$filter::class] ?? [];

        usort($registered, static fn (array $a, array $b): int => [$a['priority'], $a['order']] <=> [$b['priority'], $b['order']]);

        foreach ($registered as $entry) {
            ($this->resolve($entry['callback']))($filter);
        }

        return $filter;
    }

    /**
     * @param  Closure|class-string|array{0: class-string, 1: string}  $callback
     * @return callable(Filter): mixed
     */
    private function resolve(Closure|string|array $callback): callable
    {
        if ($callback instanceof Closure) {
            return $callback;
        }

        if (is_string($callback)) {
            $handler = $this->container->make($callback);
        } else {
            [$class, $method] = $callback;
            $handler = [$this->container->make($class), $method];
        }

        if (! is_callable($handler)) {
            throw new InvalidArgumentException('The registered hook callback is not callable.');
        }

        return $handler;
    }
}

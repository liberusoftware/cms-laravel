<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Blocks;

use Liberu\Cms\Contracts\Block\BlockTypeInterface;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Hello\Hooks\GreetingFilter;

/**
 * A block that renders a greeting. Before emitting, it runs the message through
 * hello's own {@see GreetingFilter} hook point, so another extension can adapt
 * the greeting without touching this block.
 */
final class GreetingBlock implements BlockTypeInterface
{
    public function key(): string
    {
        return 'greeting';
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    public function render(array $data, string $childrenHtml = ''): string
    {
        $name = is_string($data['name'] ?? null) ? $data['name'] : 'world';

        $message = app(HookBusInterface::class)
            ->apply(new GreetingFilter("Hello, {$name}!"))
            ->message;

        return '<div class="cms-block-greeting">'.e($message).$childrenHtml.'</div>';
    }
}

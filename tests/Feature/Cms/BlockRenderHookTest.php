<?php

declare(strict_types=1);

use Liberu\Cms\Contracts\Block\BlockRendererInterface;
use Liberu\Cms\Contracts\Hooks\Filters\BlockRenderFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;
use Liberu\Cms\Core\Hooks\HookBus;

it('lets an extension transform block output through the BlockRenderFilter hook', function (): void {
    app(HookBusInterface::class)->listen(BlockRenderFilter::class, function (BlockRenderFilter $filter): void {
        $filter->html = '<section data-hooked>'.$filter->html.'</section>';
    });

    $html = app(BlockRendererInterface::class)->render([
        'type' => 'text',
        'data' => ['text' => 'Hello'],
    ]);

    expect($html)->toStartWith('<section data-hooked>')
        ->and($html)->toEndWith('</section>')
        ->and($html)->toContain('cms-block-text');
});

it('exposes the block type and data to the hook as read-only context', function (): void {
    $seen = null;

    app(HookBusInterface::class)->listen(BlockRenderFilter::class, function (BlockRenderFilter $filter) use (&$seen): void {
        $seen = [$filter->blockType, $filter->data];
    });

    app(BlockRendererInterface::class)->render([
        'type' => 'heading',
        'data' => ['level' => 2, 'text' => 'Title'],
    ]);

    expect($seen)->toBe(['heading', ['level' => 2, 'text' => 'Title']]);
});

it('leaves block output untouched when no hook is registered', function (): void {
    $html = app(BlockRendererInterface::class)->render([
        'type' => 'text',
        'data' => ['text' => 'Plain'],
    ]);

    expect($html)->toContain('cms-block-text')
        ->and($html)->not->toContain('data-hooked');
});

it('resolves the container-bound HookBus, so a bus rebound before render is honoured', function (): void {
    $rebound = new HookBus(app());
    $rebound->listen(BlockRenderFilter::class, function (BlockRenderFilter $filter): void {
        $filter->html = 'from-rebound-bus';
    });
    app()->instance(HookBusInterface::class, $rebound);

    $html = app(BlockRendererInterface::class)->render(['type' => 'text', 'data' => ['text' => 'x']]);

    expect($html)->toBe('from-rebound-bus');
});

it('does not fire the block hook for an unknown block type', function (): void {
    $fired = false;

    app(HookBusInterface::class)->listen(BlockRenderFilter::class, function () use (&$fired): void {
        $fired = true;
    });

    expect(app(BlockRendererInterface::class)->render(['type' => 'nope', 'data' => []]))->toBe('')
        ->and($fired)->toBeFalse();
});

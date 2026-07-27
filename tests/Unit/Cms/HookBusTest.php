<?php

declare(strict_types=1);

use Liberu\Cms\Contracts\Hooks\Filter;
use Liberu\Cms\Core\Hooks\HookBus;

final class TestStringFilter implements Filter
{
    public function __construct(public string $value = '') {}

    public function name(): string
    {
        return 'test.string';
    }
}

final class TestOtherFilter implements Filter
{
    public string $value = '';

    public function name(): string
    {
        return 'test.other';
    }
}

final class AppendBangHook
{
    public function __invoke(Filter $filter): void
    {
        if ($filter instanceof TestStringFilter) {
            $filter->value .= '!';
        }
    }
}

final class AppendMethodHook
{
    public function append(Filter $filter): void
    {
        if ($filter instanceof TestStringFilter) {
            $filter->value .= '?';
        }
    }
}

it('returns the filter unchanged when nothing is registered', function (): void {
    $filter = new TestStringFilter('untouched');

    $result = (new HookBus(app()))->apply($filter);

    expect($result)->toBe($filter)
        ->and($result->value)->toBe('untouched');
});

it('runs a registered callback that mutates the payload in place', function (): void {
    $bus = new HookBus(app());
    $bus->listen(TestStringFilter::class, function (TestStringFilter $f): void {
        $f->value .= 'b';
    });

    expect($bus->apply(new TestStringFilter('a'))->value)->toBe('ab');
});

it('applies callbacks in ascending priority order, ties by registration order', function (): void {
    $bus = new HookBus(app());

    $bus->listen(TestStringFilter::class, fn (TestStringFilter $f) => $f->value .= 'third', 10);
    $bus->listen(TestStringFilter::class, fn (TestStringFilter $f) => $f->value .= 'first', -5);
    $bus->listen(TestStringFilter::class, fn (TestStringFilter $f) => $f->value .= 'second');
    $bus->listen(TestStringFilter::class, fn (TestStringFilter $f) => $f->value .= 'fourth', 10);

    expect($bus->apply(new TestStringFilter)->value)->toBe('firstsecondthirdfourth');
});

it('only runs callbacks registered for the applied filter class', function (): void {
    $bus = new HookBus(app());
    $bus->listen(TestOtherFilter::class, fn (TestOtherFilter $f) => $f->value .= 'x');

    expect($bus->apply(new TestStringFilter('kept'))->value)->toBe('kept');
});

it('resolves a class-string callback through the container', function (): void {
    $bus = new HookBus(app());
    $bus->listen(TestStringFilter::class, AppendBangHook::class);

    expect($bus->apply(new TestStringFilter('hi'))->value)->toBe('hi!');
});

it('resolves a [class, method] callback through the container', function (): void {
    $bus = new HookBus(app());
    $bus->listen(TestStringFilter::class, [AppendMethodHook::class, 'append']);

    expect($bus->apply(new TestStringFilter('q'))->value)->toBe('q?');
});

it('throws when a registered callback cannot be resolved to something callable', function (): void {
    $bus = new HookBus(app());
    $bus->listen(TestStringFilter::class, [AppendMethodHook::class, 'missingMethod']);

    expect(fn () => $bus->apply(new TestStringFilter))
        ->toThrow(InvalidArgumentException::class);
});

<?php

declare(strict_types=1);

namespace Liberu\Cms\Themes;

use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Theme\ThemeActivated;
use Liberu\Cms\Contracts\Theme\ThemeInterface;
use Liberu\Cms\Contracts\Theme\ThemeManagerInterface;
use Liberu\Cms\Themes\Exceptions\UnknownTheme;

final class ThemeManager implements ThemeManagerInterface
{
    /**
     * @var array<string, ThemeInterface>
     */
    private array $themes = [];

    public function __construct(
        private readonly ThemeStateRepository $state,
        private readonly EventBusInterface $events,
        private readonly ?string $defaultTheme = null,
    ) {}

    public function register(ThemeInterface $theme): void
    {
        $this->themes[$theme->key()] = $theme;
    }

    public function all(): array
    {
        return $this->themes;
    }

    public function get(string $key): ?ThemeInterface
    {
        return $this->themes[$key] ?? null;
    }

    public function active(): ?ThemeInterface
    {
        $key = $this->state->activeKey() ?? $this->defaultTheme;

        return $key !== null ? $this->get($key) : null;
    }

    public function setActive(string $key): void
    {
        if (! isset($this->themes[$key])) {
            throw UnknownTheme::key($key);
        }

        $previous = $this->active()?->key();

        $this->state->setActiveKey($key);

        $this->events->dispatch(new ThemeActivated($key, $previous));
    }

    public function inheritanceChain(?string $key = null): array
    {
        $theme = $key !== null ? $this->get($key) : $this->active();

        $chain = [];
        $seen = [];

        while ($theme instanceof ThemeInterface && ! isset($seen[$theme->key()])) {
            $seen[$theme->key()] = true;
            $chain[] = $theme;

            $parent = $theme->parent();
            $theme = $parent !== null ? $this->get($parent) : null;
        }

        return $chain;
    }

    public function resolveView(string $view): ?string
    {
        $relative = str_replace('.', '/', $view).'.blade.php';

        foreach ($this->inheritanceChain() as $theme) {
            $path = rtrim($theme->viewsPath(), '/').'/'.$relative;

            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}

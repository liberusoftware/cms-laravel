<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipes;

use Liberu\Cms\Core\Module\AbstractModule;

final class SiteRecipesModule extends AbstractModule
{
    public function key(): string
    {
        return 'site-recipes';
    }

    public function name(): string
    {
        return 'Site Recipes';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

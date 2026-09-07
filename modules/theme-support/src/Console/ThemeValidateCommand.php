<?php

namespace Liberu\Foundation\Theme\Console;

use Illuminate\Console\Command;
use Liberu\Foundation\Theme\Services\ThemeManager;

final class ThemeValidateCommand extends Command
{
    #[\Override]
    protected $signature = 'theme:validate';

    #[\Override]
    protected $description = 'Validate theme manifests, packages, capabilities, assets, and inheritance';

    public function handle(ThemeManager $manager): int
    {
        foreach (array_keys($manager->getThemes()) as $theme) {
            $manager->inheritanceChain($theme);
        }$this->info('All tracked themes are valid.');

        return self::SUCCESS;
    }
}

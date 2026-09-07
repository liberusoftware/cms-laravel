<?php

namespace Liberu\Foundation\Theme\Console;

use Illuminate\Console\Command;
use Liberu\Foundation\Theme\Cache\ThemeCache;

final class ThemeClearCommand extends Command
{
    #[\Override]
    protected $signature = 'theme:clear';

    #[\Override]
    protected $description = 'Clear the deployment theme registry cache';

    public function handle(ThemeCache $cache): int
    {
        $cache->clear((string) config('theme.cache_path'));
        $this->info('Theme registry cache cleared.');

        return self::SUCCESS;
    }
}

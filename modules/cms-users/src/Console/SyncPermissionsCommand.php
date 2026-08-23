<?php

declare(strict_types=1);

namespace Liberu\Cms\Users\Console;

use Illuminate\Console\Command;
use Liberu\Cms\Users\Access\SyncPermissions;

final class SyncPermissionsCommand extends Command
{
    #[\Override]
    protected $signature = 'cms:sync-permissions';

    #[\Override]
    protected $description = 'Materialise all module-declared CMS permissions into the permission backend';

    public function handle(SyncPermissions $sync): int
    {
        $names = $sync();

        $this->components->info(sprintf('Synced %d CMS permission(s).', count($names)));

        return self::SUCCESS;
    }
}

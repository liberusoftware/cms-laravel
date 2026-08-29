<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestore;

use Liberu\Cms\Core\Module\AbstractModule;

final class BackupAndRestoreModule extends AbstractModule
{
    public function key(): string
    {
        return 'backup-and-restore';
    }

    public function name(): string
    {
        return 'Backup and Restore';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

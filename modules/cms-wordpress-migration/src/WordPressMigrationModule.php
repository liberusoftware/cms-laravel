<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration;

use Liberu\Cms\Core\Module\AbstractModule;

final class WordPressMigrationModule extends AbstractModule
{
    public function key(): string { return 'wordpress-migration'; }
    public function name(): string { return 'WordPress Migration'; }
    public function version(): string { return '0.1.0'; }
}

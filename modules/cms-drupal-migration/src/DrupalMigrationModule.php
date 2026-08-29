<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigration;

use Liberu\Cms\Core\Module\AbstractModule;

final class DrupalMigrationModule extends AbstractModule
{
    public function key(): string
    {
        return 'drupal-migration';
    }

    public function name(): string
    {
        return 'Drupal Migration';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

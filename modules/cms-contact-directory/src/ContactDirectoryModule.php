<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContactDirectoryModule extends AbstractModule
{
    public function key(): string
    {
        return 'contact-directory';
    }

    public function name(): string
    {
        return 'Contact Directory';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

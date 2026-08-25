<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement;

use Liberu\Cms\Core\Module\AbstractModule;

final class DocumentManagementModule extends AbstractModule
{
    public function key(): string
    {
        return 'document-management';
    }

    public function name(): string
    {
        return 'Document Management';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

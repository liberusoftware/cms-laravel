<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement;

use Liberu\Cms\Core\Module\AbstractModule;

final class TranslationManagementModule extends AbstractModule
{
    public function key(): string { return 'translation-management'; }
    public function name(): string { return 'Translation Management'; }
    public function version(): string { return '0.1.0'; }
}

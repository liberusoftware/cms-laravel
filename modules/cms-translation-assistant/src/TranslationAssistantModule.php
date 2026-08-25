<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant;

use Liberu\Cms\Core\Module\AbstractModule;

final class TranslationAssistantModule extends AbstractModule
{
    public function key(): string
    {
        return 'translation-assistant';
    }

    public function name(): string
    {
        return 'Translation Assistant';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

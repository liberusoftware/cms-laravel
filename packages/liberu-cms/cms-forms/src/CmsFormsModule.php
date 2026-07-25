<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Forms. Public form definitions and spam-protected submissions, emitting a
 * FormSubmitted event other modules can react to. Consumes only contracts, the
 * core module system, and the content library (for slugs).
 */
final class CmsFormsModule extends AbstractModule
{
    public function key(): string
    {
        return 'forms';
    }

    public function name(): string
    {
        return 'Forms';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

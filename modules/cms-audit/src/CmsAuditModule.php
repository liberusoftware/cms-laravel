<?php

declare(strict_types=1);

namespace Liberu\Cms\Audit;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Audit logging. Listens for security-relevant events (authentication, content
 * workflow transitions) and writes append-only audit records. Consumes only
 * contracts and the core module system, so removing it just stops auditing —
 * no emitter imports anything from here.
 */
final class CmsAuditModule extends AbstractModule
{
    public function key(): string
    {
        return 'audit';
    }

    public function name(): string
    {
        return 'Audit';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

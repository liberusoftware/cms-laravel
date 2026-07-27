<?php

declare(strict_types=1);

namespace Liberu\Cms\Audit;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;

/**
 * Maps security-relevant events onto audit records. Each handler is a thin
 * translation from an event to an {@see AuditLogger} call; nothing here imports
 * a concrete emitter, keeping the audit module a pure listener.
 */
final readonly class AuditSubscriber
{
    public function __construct(private AuditLogger $logger) {}

    public function handleLogin(Login $event): void
    {
        $this->logger->record(
            'auth.login',
            metadata: ['guard' => $event->guard],
            actor: $event->user instanceof Authenticatable ? $event->user : null,
        );
    }

    public function handleLogout(Logout $event): void
    {
        $this->logger->record(
            'auth.logout',
            metadata: ['guard' => $event->guard],
            actor: $event->user instanceof Authenticatable ? $event->user : null,
        );
    }

    public function handleFailed(Failed $event): void
    {
        $credentials = $event->credentials;
        $email = is_array($credentials) && isset($credentials['email']) && is_string($credentials['email'])
            ? $credentials['email']
            : null;

        $this->logger->record(
            'auth.failed',
            metadata: ['guard' => $event->guard],
            actorLabel: $email,
        );
    }

    public function handleContentPublished(ContentPublished $event): void
    {
        $this->logger->record(
            'content.published',
            subjectType: $event->contentType,
            subjectId: $event->contentId,
        );
    }

    public function handleContentStateChanged(ContentStateChanged $event): void
    {
        $this->logger->record(
            'content.state_changed',
            subjectType: $event->contentType,
            subjectId: $event->contentId,
            metadata: ['from' => $event->from->value, 'to' => $event->to->value],
        );
    }
}

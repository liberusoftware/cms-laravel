<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Content;

/**
 * A point-in-time snapshot of a content item, used for version history and
 * rollback (Part B §14).
 *
 * @api This interface is part of the public extension API.
 */
interface RevisionInterface
{
    /**
     * Monotonic revision number within the owning item, starting at 1.
     */
    public function revisionNumber(): int;

    /**
     * The captured attribute snapshot.
     *
     * @return array<string, mixed>
     */
    public function snapshot(): array;
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Revisions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Gives a content model point-in-time version history and rollback (Part B §14).
 *
 * `recordRevision()` snapshots the model's revisionable attributes; `revertTo()`
 * restores a numbered snapshot. By default every fillable attribute is captured;
 * override `revisionableAttributes()` to narrow it.
 *
 * @mixin Model
 */
trait HasRevisions
{
    /**
     * @return MorphMany<Revision, $this>
     */
    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'revisionable')->orderBy('revision_number');
    }

    public function recordRevision(?int $userId = null): Revision
    {
        /** @var Revision $revision */
        $revision = $this->revisions()->create([
            'revision_number' => $this->latestRevisionNumber() + 1,
            'snapshot' => $this->revisionSnapshot(),
            'user_id' => $userId,
            'created_at' => now(),
        ]);

        return $revision;
    }

    public function latestRevisionNumber(): int
    {
        $max = $this->revisions()->max('revision_number');

        return is_numeric($max) ? (int) $max : 0;
    }

    public function revertTo(int $revisionNumber): bool
    {
        $revision = $this->revisions()->where('revision_number', $revisionNumber)->first();

        if (! $revision instanceof Revision) {
            return false;
        }

        $this->forceFill($revision->snapshot())->save();

        return true;
    }

    /**
     * Capture the cast values of the revisionable attributes that are actually
     * loaded on the model. Using cast values (not raw storage) means casts such
     * as array or enum round-trip correctly through a revert; intersecting with
     * loaded attributes means a snapshot never records a null for a column that
     * simply was not hydrated.
     *
     * @return array<string, mixed>
     */
    protected function revisionSnapshot(): array
    {
        $keys = array_intersect($this->revisionableAttributes(), array_keys($this->getAttributes()));

        $snapshot = [];

        foreach ($keys as $key) {
            $snapshot[$key] = $this->getAttribute($key);
        }

        return $snapshot;
    }

    /**
     * @return array<int, string>
     */
    protected function revisionableAttributes(): array
    {
        return array_values($this->getFillable());
    }
}

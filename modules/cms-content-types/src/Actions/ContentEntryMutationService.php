<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Cms\ContentTypes\Models\ContentEntry;

/**
 * Public mutation boundary for typed content entities.
 */
final class ContentEntryMutationService
{
    public function clone(ContentEntry $entry, ?string $title = null): ContentEntry
    {
        return DB::transaction(function () use ($entry, $title): ContentEntry {
            $clone = $entry->replicate(['canonical_id', 'status', 'published_at']);
            $clone->title = $title !== null && trim($title) !== '' ? trim($title) : $entry->title.' (Copy)';
            $clone->slug = null;
            $clone->status = 'draft';
            $clone->published_at = null;
            $clone->canonical_id = null;
            $clone->save();

            foreach ($entry->relatedEntries()->get() as $related) {
                $pivot = $related->pivot;
                $clone->relatedEntries()->attach($related->getKey(), [
                    'relation' => $pivot->relation,
                    'position' => $pivot->position,
                ]);
            }

            return $clone->load('type', 'relatedEntries');
        });
    }
}

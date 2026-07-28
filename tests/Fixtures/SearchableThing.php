<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * A minimal Scout-searchable model used to prove the Scout driver wiring through
 * Scout's in-memory collection engine — no Meilisearch service required.
 *
 * @property int $id
 * @property string $title
 */
final class SearchableThing extends Model
{
    use Searchable;

    protected $table = 'searchable_things';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return ['title' => $this->title];
    }
}

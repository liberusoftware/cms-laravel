<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeeds\Models;

use Illuminate\Database\Eloquent\Model;

final class FeedItem extends Model
{
    #[\Override]
    protected $table = 'cms_feed_items';

    #[\Override]
    protected $fillable = ['feed_id', 'external_id', 'title', 'url', 'summary', 'content', 'attribution', 'dedupe_hash', 'payload', 'published_at'];

    protected function casts(): array
    {
        return ['attribution' => 'array', 'payload' => 'array', 'published_at' => 'datetime'];
    }
}

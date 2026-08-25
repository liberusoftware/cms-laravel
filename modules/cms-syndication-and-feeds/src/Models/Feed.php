<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeeds\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Feed extends Model
{
    #[\Override]
    protected $table = 'cms_feeds';

    #[\Override]
    protected $fillable = ['key', 'title', 'format', 'source_url', 'mapping', 'scheduled_at', 'active'];

    protected function casts(): array
    {
        return ['mapping' => 'array', 'scheduled_at' => 'datetime', 'active' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FeedItem::class);
    }
}

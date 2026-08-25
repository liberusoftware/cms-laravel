<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;

final class CollectionItem extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_collection_items';

    #[\Override]
    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'data', 'metadata', 'status', 'published_at', 'collection_id', 'user_id', 'team_id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'data' => 'array', 'metadata' => 'array'];
    }

    protected static function booted(): void
    {
        self::saving(function (CollectionItem $item): void {
            if (blank($item->slug) && filled($item->title)) {
                $item->slug = Slugger::unique($item, $item->title);
            }
            if (blank($item->excerpt) && filled($item->content)) {
                $item->excerpt = Str::words(strip_tags((string) $item->content), 40);
            }
        });
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function isLive(): bool
    {
        return $this->status === 'published' && $this->published_at?->isPast();
    }
}

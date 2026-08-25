<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Collection extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_collections';

    #[\Override]
    protected $fillable = ['name', 'slug', 'type', 'description', 'schema', 'team_id'];

    protected function casts(): array
    {
        return ['schema' => 'array'];
    }

    protected static function booted(): void
    {
        self::saving(function (Collection $collection): void {
            if (blank($collection->slug) && filled($collection->name)) {
                $collection->slug = Slugger::unique($collection, $collection->name);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(CollectionItem::class);
    }
}

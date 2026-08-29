<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Layout extends Model
{
    use HasTenant;

    protected $table = 'cms_layouts';

    protected $fillable = ['name', 'slug', 'target_type', 'target_id', 'definition', 'status', 'published_at', 'user_id', 'team_id'];

    protected function casts(): array
    {
        return ['definition' => 'array', 'published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        self::saving(function (self $layout): void {
            if (blank($layout->slug) && filled($layout->name)) {
                $layout->slug = Slugger::unique($layout, $layout->name);
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}

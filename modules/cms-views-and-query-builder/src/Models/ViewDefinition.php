<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilder\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ViewDefinition extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_view_definitions';

    #[\Override]
    protected $fillable = ['name', 'slug', 'source', 'description', 'definition', 'visibility', 'status', 'published_at', 'user_id', 'team_id'];

    protected function casts(): array
    {
        return ['definition' => 'array', 'published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        self::saving(function (self $view): void {
            if (blank($view->slug) && filled($view->name)) {
                $view->slug = Slugger::unique($view, $view->name);
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Posts\Database\Factories\CategoryFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $team_id
 */
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasTenant;

    #[\Override]
    protected $table = 'cms_categories';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['name', 'slug', 'description', 'team_id'];

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (Category $category): void {
            if (blank($category->slug) && filled($category->name)) {
                $category->slug = Slugger::unique($category, $category->name);
            }
        });
    }

    /**
     * @return BelongsToMany<Post, $this>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'cms_post_category');
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}

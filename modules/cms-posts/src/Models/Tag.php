<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Posts\Database\Factories\TagFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $team_id
 */
final class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    use HasTenant;

    #[\Override]
    protected $table = 'cms_tags';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['name', 'slug', 'team_id'];

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (Tag $tag): void {
            if (blank($tag->slug) && filled($tag->name)) {
                $tag->slug = Slugger::unique($tag, $tag->name);
            }
        });
    }

    /**
     * @return BelongsToMany<Post, $this>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'cms_post_tag');
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}

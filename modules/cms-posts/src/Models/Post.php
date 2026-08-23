<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Liberu\Cms\Content\Revisions\HasRevisions;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Content\Workflow\HasWorkflow;
use Liberu\Cms\Contracts\Content\PublishableInterface;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Posts\Database\Factories\PostFactory;

/**
 * A blog post with editorial workflow, versioning, taxonomy, an optional
 * featured image (resolved through the media contract), and an auto-generated
 * excerpt.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $excerpt
 * @property bool $is_featured
 * @property int|null $featured_media_id
 * @property int|null $author_id
 * @property int|null $team_id
 */
final class Post extends Model implements PublishableInterface
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use HasRevisions;
    use HasTenant;
    use HasWorkflow;

    #[\Override]
    protected $table = 'cms_posts';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'published_at',
        'is_featured',
        'featured_media_id',
        'author_id',
        'team_id',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['is_featured' => 'boolean'];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (Post $post): void {
            if (blank($post->slug) && filled($post->title)) {
                $post->slug = Slugger::unique($post, $post->title);
            }

            if (blank($post->excerpt) && filled($post->content)) {
                $post->excerpt = Str::words(strip_tags((string) $post->content), 40);
            }
        });
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'cms_post_category');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'cms_post_tag');
    }

    public function featuredMedia(): ?MediaItemInterface
    {
        if ($this->featured_media_id === null) {
            return null;
        }

        return app(MediaRepositoryInterface::class)->find($this->featured_media_id);
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }
}

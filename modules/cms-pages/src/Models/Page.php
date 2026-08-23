<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Content\Revisions\HasRevisions;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Content\Workflow\HasWorkflow;
use Liberu\Cms\Contracts\Content\PublishableInterface;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Pages\Database\Factories\PageFactory;

/**
 * A hierarchical page with editorial workflow, versioning, and an optional
 * featured media item resolved through the media contract.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $excerpt
 * @property string $template
 * @property int|null $parent_id
 * @property int|null $featured_media_id
 * @property int|null $team_id
 * @property int|null $user_id
 */
final class Page extends Model implements PublishableInterface
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    use HasRevisions;
    use HasTenant;
    use HasWorkflow;

    #[\Override]
    protected $table = 'cms_pages';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'template',
        'status',
        'published_at',
        'parent_id',
        'featured_media_id',
        'team_id',
        'user_id',
    ];

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (Page $page): void {
            if (blank($page->slug) && filled($page->title)) {
                $page->slug = Slugger::unique($page, $page->title);
            }
        });
    }

    /**
     * @return BelongsTo<Page, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<Page, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function featuredMedia(): ?MediaItemInterface
    {
        if ($this->featured_media_id === null) {
            return null;
        }

        return app(MediaRepositoryInterface::class)->find($this->featured_media_id);
    }

    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }
}

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
 * @property bool $is_home
 * @property bool $is_error
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
        'is_home',
        'is_error',
    ];

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (Page $page): void {
            if (blank($page->slug) && filled($page->title)) {
                $page->slug = Slugger::unique($page, $page->title);
            }

            if ($page->parent_id !== null && (int) $page->parent_id === (int) $page->getKey()) {
                throw new \InvalidArgumentException('A page cannot be its own parent.');
            }
        });

        self::saved(function (Page $page): void {
            if ($page->wasChanged('is_home') && $page->is_home) {
                self::query()
                    ->when($page->team_id === null, fn ($query) => $query->whereNull('team_id'), fn ($query) => $query->where('team_id', $page->team_id))
                    ->where($page->getKeyName(), '!=', $page->getKey())
                    ->where('is_home', true)
                    ->update(['is_home' => false]);
            }

            if ($page->wasChanged('is_error') && $page->is_error) {
                self::query()
                    ->when($page->team_id === null, fn ($query) => $query->whereNull('team_id'), fn ($query) => $query->where('team_id', $page->team_id))
                    ->where($page->getKeyName(), '!=', $page->getKey())
                    ->where('is_error', true)
                    ->update(['is_error' => false]);
            }
        });
    }

    protected function casts(): array
    {
        return ['is_home' => 'boolean', 'is_error' => 'boolean', 'published_at' => 'datetime'];
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

    /** @return HasMany<PageAlias, $this> */
    public function aliases(): HasMany
    {
        return $this->hasMany(PageAlias::class);
    }

    /**
     * Return the canonical URL path assembled from every ancestor slug.
     */
    public function path(): string
    {
        $segments = [];
        $page = $this;
        $seen = [];

        while ($page !== null && ! isset($seen[$page->getKey()])) {
            $seen[$page->getKey()] = true;
            array_unshift($segments, trim((string) $page->slug, '/'));
            $page = $page->relationLoaded('parent') ? $page->parent : $page->parent()->first();
        }

        return '/'.implode('/', array_filter($segments));
    }

    /** @return list<Page> */
    public function breadcrumbs(): array
    {
        $trail = [];
        $page = $this;
        $seen = [];

        while ($page !== null && ! isset($seen[$page->getKey()])) {
            $seen[$page->getKey()] = true;
            array_unshift($trail, $page);
            $page = $page->relationLoaded('parent') ? $page->parent : $page->parent()->first();
        }

        return $trail;
    }

    public function addAlias(string $path): PageAlias
    {
        $path = '/'.trim($path, '/');

        return $this->aliases()->firstOrCreate(['path' => $path], ['team_id' => $this->team_id]);
    }

    public function isHome(): bool
    {
        return (bool) $this->is_home;
    }

    public function isErrorPage(): bool
    {
        return (bool) $this->is_error;
    }

    public function markAsHome(): void
    {
        self::query()
            ->when($this->team_id === null, fn ($query) => $query->whereNull('team_id'), fn ($query) => $query->where('team_id', $this->team_id))
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->update(['is_home' => false]);
        $this->forceFill(['is_home' => true])->save();
    }

    public function markAsError(): void
    {
        self::query()
            ->when($this->team_id === null, fn ($query) => $query->whereNull('team_id'), fn ($query) => $query->where('team_id', $this->team_id))
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->update(['is_error' => false]);
        $this->forceFill(['is_error' => true])->save();
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

<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Content\Revisions\HasRevisions;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Content\Workflow\HasWorkflow;
use Liberu\Cms\ContentTypes\Database\Factories\ContentEntryFactory;
use Liberu\Cms\Contracts\Content\PublishableInterface;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * A content item belonging to a custom content type, whose `data` conforms to
 * that type's field schema. Fully workflow- and revision-enabled.
 *
 * @property int $id
 * @property int $content_type_id
 * @property string $title
 * @property string $slug
 * @property array<string, mixed>|null $data
 * @property string|null $canonical_id
 * @property int|null $author_id
 * @property int|null $team_id
 */
final class ContentEntry extends Model implements PublishableInterface
{
    /** @use HasFactory<ContentEntryFactory> */
    use HasFactory;

    use HasRevisions;
    use HasTenant;
    use HasWorkflow;

    #[\Override]
    protected $table = 'cms_content_entries';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['content_type_id', 'title', 'slug', 'data', 'status', 'published_at', 'author_id', 'team_id'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (ContentEntry $entry): void {
            if (blank($entry->slug) && filled($entry->title)) {
                $entry->slug = Slugger::unique($entry, $entry->title);
            }
            if (blank($entry->canonical_id) && filled($entry->slug)) {
                $type = $entry->type;
                $entry->canonical_id = ($type instanceof ContentType ? $type->key : 'entry').':'.$entry->slug;
            }
        });
    }

    /**
     * @return BelongsTo<ContentType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    /**
     * Report the custom type's key as the content type in workflow events,
     * overriding the trait's class-based default.
     */
    public function contentType(): string
    {
        $type = $this->type;

        return $type instanceof ContentType ? $type->key : 'entry';
    }

    public function authorId(): int|string|null
    {
        return $this->author_id;
    }

    /**
     * @return BelongsToMany<self, $this>
     */
    public function relatedEntries(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'cms_content_entry_relationships', 'source_entry_id', 'target_entry_id')
            ->withPivot(['relation', 'position'])
            ->orderByPivot('position');
    }

    /**
     * @return BelongsToMany<self, $this>
     */
    public function relatedFromEntries(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'cms_content_entry_relationships', 'target_entry_id', 'source_entry_id')
            ->withPivot(['relation', 'position'])
            ->orderByPivot('position');
    }

    public function relateTo(self $entry, string $relation = 'related', int $position = 0): void
    {
        if ($this->team_id !== $entry->team_id) {
            throw ValidationException::withMessages(['relation' => 'Related entities must belong to the same tenant.']);
        }

        $this->relatedEntries()->syncWithoutDetaching([
            $entry->getKey() => ['relation' => $relation, 'position' => $position],
        ]);
    }

    public function cloneEntity(?string $title = null): self
    {
        $clone = $this->replicate(['canonical_id', 'status', 'published_at']);
        $clone->title = $title ?? $this->title.' (Copy)';
        $clone->slug = null;
        $clone->status = WorkflowState::Draft;
        $clone->published_at = null;
        $clone->canonical_id = null;
        $clone->save();

        return $clone;
    }

    protected static function newFactory(): ContentEntryFactory
    {
        return ContentEntryFactory::new();
    }
}

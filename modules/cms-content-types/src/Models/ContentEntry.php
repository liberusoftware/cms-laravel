<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Content\Revisions\HasRevisions;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Content\Workflow\HasWorkflow;
use Liberu\Cms\ContentTypes\Database\Factories\ContentEntryFactory;
use Liberu\Cms\Contracts\Content\PublishableInterface;
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
    protected $fillable = ['content_type_id', 'title', 'slug', 'data', 'status', 'published_at', 'team_id'];

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

    protected static function newFactory(): ContentEntryFactory
    {
        return ContentEntryFactory::new();
    }
}

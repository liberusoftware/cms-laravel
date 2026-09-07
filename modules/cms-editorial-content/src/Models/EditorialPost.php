<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $public_id
 * @property string $slug
 * @property string $title
 * @property string|null $excerpt
 * @property string|null $body
 * @property string $status
 * @property bool $featured
 * @property Carbon|null $published_at
 * @property Carbon|null $archived_at
 * @property int|null $author_id
 * @property int|null $series_id
 * @property array<int, string>|null $categories
 * @property array<int, string>|null $tags
 * @property array<int, string>|null $related_public_ids
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class EditorialPost extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_editorial_posts';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'slug', 'title', 'excerpt', 'body', 'status', 'featured', 'published_at', 'archived_at', 'author_id', 'series_id', 'categories', 'tags', 'related_public_ids'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'featured' => 'boolean', 'published_at' => 'datetime', 'archived_at' => 'datetime', 'categories' => 'array', 'tags' => 'array', 'related_public_ids' => 'array'];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Comment extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_comments';

    #[\Override]
    protected $fillable = ['team_id', 'commentable_type', 'commentable_id', 'parent_id', 'author_id', 'guest_name', 'guest_email', 'body', 'status', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'edited_at' => 'datetime', 'moderated_at' => 'datetime'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommentReport::class);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Revisions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Liberu\Cms\Contracts\Content\RevisionInterface;

/**
 * @property int $id
 * @property string $revisionable_type
 * @property int $revisionable_id
 * @property int $revision_number
 * @property array<string, mixed>|null $snapshot
 * @property int|null $user_id
 */
final class Revision extends Model implements RevisionInterface
{
    #[\Override]
    protected $table = 'cms_revisions';

    #[\Override]
    public $timestamps = false;

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = [
        'revisionable_type',
        'revisionable_id',
        'revision_number',
        'snapshot',
        'user_id',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return [
            'revision_number' => 'integer',
            'snapshot' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function revisionNumber(): int
    {
        return $this->revision_number;
    }

    public function snapshot(): array
    {
        return $this->snapshot ?? [];
    }
}

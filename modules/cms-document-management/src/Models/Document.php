<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|null $team_id
 * @property string $title
 * @property string $slug
 * @property string|null $path
 * @property string|null $mime_type
 * @property int|null $size
 * @property string $status
 * @property Carbon|null $retention_until
 */
final class Document extends Model
{
    #[\Override]
    protected $table = 'cms_documents';

    #[\Override]
    protected $fillable = ['team_id', 'title', 'slug', 'path', 'mime_type', 'size', 'status', 'extracted_text', 'retention_until'];

    protected function casts(): array
    {
        return ['retention_until' => 'datetime'];
    }

    /** @return HasMany<DocumentDownload, $this> */
    public function downloads(): HasMany
    {
        return $this->hasMany(DocumentDownload::class);
    }
}

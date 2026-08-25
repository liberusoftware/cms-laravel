<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Document extends Model
{
    protected $table = 'cms_documents';

    protected $fillable = ['team_id', 'title', 'slug', 'path', 'mime_type', 'size', 'status', 'extracted_text', 'retention_until'];

    protected function casts(): array
    {
        return ['retention_until' => 'datetime'];
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(DocumentDownload::class);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement\Models;

use Illuminate\Database\Eloquent\Model;

final class DocumentDownload extends Model
{
    #[\Override]
    protected $table = 'cms_document_downloads';

    #[\Override]
    protected $fillable = ['user_id', 'ip_address', 'downloaded_at'];

    protected function casts(): array
    {
        return ['downloaded_at' => 'datetime'];
    }
}

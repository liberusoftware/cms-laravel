<?php

declare(strict_types=1);

namespace Liberu\Cms\Metadata\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class MetadataEntry extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_metadata_entries';

    #[\Override]
    protected $fillable = ['subject_type', 'subject_id', 'key', 'value', 'value_type', 'team_id'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }
}

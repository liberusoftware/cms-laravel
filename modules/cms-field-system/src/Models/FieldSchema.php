<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class FieldSchema extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_field_schemas';

    #[\Override]
    protected $fillable = ['team_id', 'key', 'name', 'version', 'fields', 'history'];

    protected function casts(): array
    {
        return ['version' => 'integer', 'fields' => 'array', 'history' => 'array'];
    }
}

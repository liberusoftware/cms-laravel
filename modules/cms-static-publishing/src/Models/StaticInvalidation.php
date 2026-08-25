<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Models;

use Illuminate\Database\Eloquent\Model;

final class StaticInvalidation extends Model
{
    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected $table = 'cms_static_invalidations';

    #[\Override]
    protected $fillable = ['build_id', 'path', 'reason', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}

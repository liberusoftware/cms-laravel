<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgets\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Widget extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_widgets';

    #[\Override]
    protected $fillable = ['key', 'type', 'title', 'configuration', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['configuration' => 'array', 'active' => 'boolean'];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModes\Models;

use Illuminate\Database\Eloquent\Model;

final class DisplayMode extends Model
{
    protected $table = 'cms_display_modes';

    protected $fillable = ['team_id', 'name', 'slug', 'content_type', 'mode_type', 'formatters', 'configuration', 'responsive_variants', 'projection', 'active'];

    protected function casts(): array
    {
        return ['formatters' => 'array', 'configuration' => 'array', 'responsive_variants' => 'array', 'projection' => 'array', 'active' => 'boolean'];
    }
}

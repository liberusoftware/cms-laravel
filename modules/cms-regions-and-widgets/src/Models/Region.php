<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Region extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_regions';

    #[\Override]
    protected $fillable = ['key', 'label', 'theme', 'team_id'];

    public function widgets(): BelongsToMany
    {
        return $this->belongsToMany(Widget::class, 'cms_widget_placements')->withPivot(['position', 'visibility', 'starts_at', 'ends_at', 'active'])->orderBy('position');
    }
}

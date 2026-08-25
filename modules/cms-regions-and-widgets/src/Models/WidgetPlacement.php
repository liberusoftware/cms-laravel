<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WidgetPlacement extends Model
{
    #[\Override]
    protected $table = 'cms_widget_placements';

    #[\Override]
    protected $fillable = ['region_id', 'widget_id', 'position', 'visibility', 'starts_at', 'ends_at', 'active'];

    protected function casts(): array
    {
        return ['visibility' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'active' => 'boolean', 'position' => 'integer'];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(Widget::class);
    }
}

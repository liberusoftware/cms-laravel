<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Contracts\Events\Core\SettingChanged;
use Liberu\Cms\Contracts\Events\EventBusInterface;

final class Setting extends Model
{
    protected $table = 'cms_settings';

    protected $fillable = ['site_id', 'key', 'value', 'environment'];

    protected function casts(): array
    {
        return ['value' => 'array'];
    }

    protected static function booted(): void
    {
        self::saved(function (Setting $setting): void {
            app(EventBusInterface::class)->dispatch(new SettingChanged($setting->getKey(), $setting->site_id, $setting->key));
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}

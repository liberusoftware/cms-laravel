<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Liberu\Cms\Contracts\Events\Core\SettingChanged;
use Liberu\Cms\Contracts\Events\EventBusInterface;

/**
 * @property int $id
 * @property int|null $site_id
 * @property string $key
 * @property array<string, mixed>|null $value
 * @property string $environment
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 */
final class Setting extends Model
{
    #[\Override]
    protected $table = 'cms_settings';

    #[\Override]
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

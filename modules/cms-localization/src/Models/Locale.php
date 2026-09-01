<?php

declare(strict_types=1);

namespace Liberu\Cms\Localization\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property string $locale
 * @property string|null $fallback_locale
 * @property string $direction
 * @property bool $enabled
 */
final class Locale extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_localization_locales';

    #[\Override]
    protected $fillable = ['team_id', 'locale', 'fallback_locale', 'direction', 'enabled'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'enabled' => 'boolean'];
    }
}

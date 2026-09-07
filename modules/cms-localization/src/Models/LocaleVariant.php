<?php

declare(strict_types=1);

namespace Liberu\Cms\Localization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $public_id
 * @property string $source_type
 * @property string $source_key
 * @property string $field
 * @property string $locale
 * @property string $value
 * @property string|null $localized_slug
 * @property string $status
 * @property Carbon|null $completed_at
 */
final class LocaleVariant extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_localization_variants';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'source_type', 'source_key', 'field', 'locale', 'value', 'localized_slug', 'status', 'completed_at'];

    protected function casts(): array
    {
        return ['team_id' => 'integer', 'completed_at' => 'datetime'];
    }
}

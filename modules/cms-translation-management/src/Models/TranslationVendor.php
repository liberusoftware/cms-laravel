<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TranslationVendor extends Model
{
    use HasTenant;

    protected $table = 'cms_translation_vendors';

    protected $fillable = ['key', 'name', 'driver', 'settings', 'status', 'team_id'];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }
}

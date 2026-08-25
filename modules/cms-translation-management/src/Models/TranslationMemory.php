<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TranslationMemory extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_translation_memory';

    #[\Override]
    protected $fillable = ['source_locale', 'target_locale', 'source_hash', 'source_text', 'translated_text', 'status', 'metadata', 'team_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}

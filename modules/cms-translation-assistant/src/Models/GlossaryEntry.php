<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class GlossaryEntry extends Model
{
    use HasTenant;
    protected $table = 'cms_translation_glossary';
    protected $fillable = ['source_locale', 'target_locale', 'source_term', 'preferred_term', 'forbidden_terms', 'team_id'];
    protected function casts(): array { return ['forbidden_terms' => 'array']; }
}

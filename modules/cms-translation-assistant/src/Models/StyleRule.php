<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class StyleRule extends Model
{
    use HasTenant;

    protected $table = 'cms_translation_style_rules';

    protected $fillable = ['locale', 'name', 'pattern', 'message', 'severity', 'team_id'];
}

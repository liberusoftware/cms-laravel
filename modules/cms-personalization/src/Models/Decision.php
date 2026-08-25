<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Decision extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_personalization_decisions';

    #[\Override]
    protected $fillable = ['audience_key', 'variant_key', 'subject_key', 'context', 'reason', 'team_id'];

    protected function casts(): array
    {
        return ['context' => 'array'];
    }
}

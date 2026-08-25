<?php

declare(strict_types=1);

namespace Liberu\Cms\Personalization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Audience extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_personalization_audiences';

    #[\Override]
    protected $fillable = ['name', 'key', 'rules', 'requires_consent', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['rules' => 'array', 'requires_consent' => 'boolean', 'active' => 'boolean'];
    }

    /** @return HasMany<Variant, $this> */
    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    /** @return HasMany<Decision, $this> */
    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class, 'audience_key', 'key');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Taxonomy extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_taxonomies';

    #[\Override]
    protected $fillable = ['key', 'name', 'description', 'hierarchical', 'exclusive', 'team_id'];

    protected function casts(): array
    {
        return ['hierarchical' => 'boolean', 'exclusive' => 'boolean'];
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}

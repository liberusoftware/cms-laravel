<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class SiteRecipe extends Model
{
    use HasTenant;

    protected $table = 'cms_site_recipes';

    protected $fillable = ['key', 'name', 'description', 'status', 'team_id'];

    public function versions(): HasMany
    {
        return $this->hasMany(SiteRecipeVersion::class, 'recipe_id')->orderByDesc('version');
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SiteRecipeVersion extends Model
{
    #[\Override]
    protected $table = 'cms_site_recipe_versions';

    #[\Override]
    protected $fillable = ['recipe_id', 'version', 'modules', 'configuration', 'content_types', 'workflows', 'menus', 'blocks', 'themes', 'starter_content', 'checksum', 'author_id'];

    protected function casts(): array
    {
        return ['modules' => 'array', 'configuration' => 'array', 'content_types' => 'array', 'workflows' => 'array', 'menus' => 'array', 'blocks' => 'array', 'themes' => 'array', 'starter_content' => 'array', 'version' => 'integer'];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(SiteRecipe::class, 'recipe_id');
    }
}

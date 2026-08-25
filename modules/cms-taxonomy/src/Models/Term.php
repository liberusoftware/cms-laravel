<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class Term extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_taxonomy_terms';

    #[\Override]
    protected $fillable = ['taxonomy_id', 'parent_id', 'slug', 'name', 'description', 'synonyms', 'translations', 'position', 'team_id'];

    protected function casts(): array
    {
        return ['synonyms' => 'array', 'translations' => 'array', 'position' => 'integer'];
    }

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position')->orderBy('name');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TermAssignment::class);
    }

    /** @return array<int, self> */
    public function descendants(): array
    {
        $result = [];
        $queue = $this->children()->get()->all();
        while ($queue !== []) {
            $child = array_shift($queue);
            $result[] = $child;
            $queue = [...$queue, ...$child->children()->get()->all()];
        }

        return $result;
    }
}

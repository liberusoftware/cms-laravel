<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Menus\Database\Factories\MenuFactory;

/**
 * A named navigation menu assigned to a location (header, footer, sidebar,
 * mobile, …).
 *
 * @property int $id
 * @property string $name
 * @property string $location
 * @property int|null $team_id
 */
final class Menu extends Model
{
    /** @use HasFactory<MenuFactory> */
    use HasFactory;

    use HasTenant;

    #[\Override]
    protected $table = 'cms_menus';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['name', 'location', 'team_id'];

    /**
     * @return HasMany<MenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort');
    }

    protected static function newFactory(): MenuFactory
    {
        return MenuFactory::new();
    }
}

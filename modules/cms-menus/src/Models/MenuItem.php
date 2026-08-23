<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Menus\Database\Factories\MenuItemFactory;

/**
 * A single link in a menu. May nest via parent_id, and may require a permission
 * to be shown (role/permission-based menus).
 *
 * @property int $id
 * @property int $menu_id
 * @property int|null $parent_id
 * @property string $label
 * @property string $url
 * @property int $sort
 * @property string|null $permission
 * @property int|null $team_id
 */
final class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory;

    use HasTenant;

    #[\Override]
    protected $table = 'cms_menu_items';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['menu_id', 'parent_id', 'label', 'url', 'sort', 'permission', 'team_id'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['sort' => 'integer'];
    }

    /**
     * @return BelongsTo<Menu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return BelongsTo<MenuItem, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    protected static function newFactory(): MenuItemFactory
    {
        return MenuItemFactory::new();
    }
}

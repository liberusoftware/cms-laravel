<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;

/**
 * @extends Factory<MenuItem>
 */
final class MenuItemFactory extends Factory
{
    #[\Override]
    protected $model = MenuItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'label' => $this->faker->words(2, true),
            'url' => '/'.$this->faker->slug(),
            'sort' => 0,
            'permission' => null,
        ];
    }
}

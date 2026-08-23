<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Menus\Models\Menu;

/**
 * @extends Factory<Menu>
 */
final class MenuFactory extends Factory
{
    #[\Override]
    protected $model = Menu::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'location' => 'header',
        ];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Posts\Models\Tag;

/**
 * @extends Factory<Tag>
 */
final class TagFactory extends Factory
{
    #[\Override]
    protected $model = Tag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => $name,
            'slug' => str($name)->slug()->value(),
        ];
    }
}

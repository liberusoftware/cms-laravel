<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\CollectionItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

use function Illuminate\Support\now;

/**
 * @extends Factory<CollectionItem>
 */
class CollectionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'slug' => fake()->unique()->slug(),
            'content' => fake()->paragraphs(3, true),
            'published_at' => now(),
            'status' => 'published',
            'collection_id' => Collection::factory(),
            'user_id' => User::factory(),
        ];
    }
}

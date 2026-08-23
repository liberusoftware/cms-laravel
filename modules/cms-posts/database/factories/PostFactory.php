<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Posts\Models\Post;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    #[\Override]
    protected $model = Post::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => str($title)->slug()->value(),
            'content' => $this->faker->paragraphs(4, true),
            'status' => WorkflowState::Draft->value,
            'published_at' => null,
            'is_featured' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => WorkflowState::Published->value,
            'published_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }
}

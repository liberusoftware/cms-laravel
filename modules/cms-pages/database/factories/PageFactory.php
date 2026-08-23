<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Pages\Models\Page;

/**
 * @extends Factory<Page>
 */
final class PageFactory extends Factory
{
    #[\Override]
    protected $model = Page::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => str($title)->slug()->value(),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence(),
            'template' => 'default',
            'status' => WorkflowState::Draft->value,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => WorkflowState::Published->value,
            'published_at' => now(),
        ]);
    }

    public function home(): static
    {
        return $this->state(fn (): array => [
            'title' => 'Home',
            'slug' => 'home',
            'template' => 'home',
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * @extends Factory<ContentEntry>
 */
final class ContentEntryFactory extends Factory
{
    #[\Override]
    protected $model = ContentEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'content_type_id' => ContentType::factory(),
            'title' => $title,
            'slug' => str($title)->slug()->value(),
            'data' => ['summary' => $this->faker->sentence()],
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
}

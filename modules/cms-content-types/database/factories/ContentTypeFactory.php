<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\ContentTypes\Models\ContentType;

/**
 * @extends Factory<ContentType>
 */
final class ContentTypeFactory extends Factory
{
    #[\Override]
    protected $model = ContentType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = $this->faker->unique()->slug(2);

        return [
            'key' => $key,
            'name' => $this->faker->words(2, true),
            'singular_label' => 'Item',
            'plural_label' => 'Items',
            'fields' => [
                ['name' => 'summary', 'label' => 'Summary', 'type' => 'text', 'required' => true],
                ['name' => 'year', 'label' => 'Year', 'type' => 'number', 'required' => false],
            ],
        ];
    }
}

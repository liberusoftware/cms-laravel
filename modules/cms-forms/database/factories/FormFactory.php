<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Forms\Models\Form;

/**
 * @extends Factory<Form>
 */
final class FormFactory extends Factory
{
    #[\Override]
    protected $model = Form::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => str($name)->slug()->value(),
            'fields' => [
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
            ],
        ];
    }
}

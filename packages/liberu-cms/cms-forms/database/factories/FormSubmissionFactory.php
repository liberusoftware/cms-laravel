<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Liberu\Cms\Forms\Models\Form;
use Liberu\Cms\Forms\Models\FormSubmission;

/**
 * @extends Factory<FormSubmission>
 */
final class FormSubmissionFactory extends Factory
{
    #[\Override]
    protected $model = FormSubmission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => Form::factory(),
            'data' => [
                'email' => $this->faker->safeEmail(),
                'message' => $this->faker->sentence(),
            ],
            'meta' => ['ip' => $this->faker->ipv4()],
        ];
    }
}

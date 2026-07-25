<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Support;

use Liberu\Cms\Forms\Models\Form;

/**
 * Validates a raw submission against a form's field schema, returning only the
 * defined fields' values. Throws a ValidationException (rendered as 422) when a
 * required field is missing or a value fails its type rule.
 */
final class SubmissionValidator
{
    /**
     * @param  array<array-key, mixed>  $input
     * @return array<string, mixed>
     */
    public function validate(Form $form, array $input): array
    {
        $rules = [];
        $attributes = [];

        foreach ($form->fieldDefinitions() as $field) {
            if ($field->name === '') {
                continue;
            }

            $rules[$field->name] = [$field->required ? 'required' : 'nullable', $field->type->rule()];
            $attributes[$field->name] = $field->label !== '' ? $field->label : $field->name;
        }

        /** @var array<string, mixed> $validated */
        $validated = validator($input, $rules, [], $attributes)->validate();

        return $validated;
    }
}

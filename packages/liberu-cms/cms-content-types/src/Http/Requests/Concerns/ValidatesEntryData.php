<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Requests\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Liberu\Cms\ContentTypes\Fields\FieldDefinition;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\ContentTypes\Schema\InvalidContentData;
use Liberu\Cms\ContentTypes\Schema\SchemaValidator;

/**
 * Validates a Content-Entry's `data` payload against its content type's field
 * schema, adding any violation as a `data.<field>` validation error (422). The
 * required/type rules are delegated to {@see SchemaValidator} so the mapping is
 * not duplicated; unknown fields are rejected rather than silently dropped.
 *
 * @mixin FormRequest
 */
trait ValidatesEntryData
{
    protected function validateEntryData(Validator $validator, ?ContentType $type): void
    {
        if (! $type instanceof ContentType) {
            return;
        }

        $input = $this->input('data');

        /** @var array<string, mixed> $data */
        $data = is_array($input) ? $input : [];

        $allowed = array_map(static fn (FieldDefinition $field): string => $field->name, $type->fieldDefinitions());

        foreach (array_keys($data) as $key) {
            if (! in_array($key, $allowed, true)) {
                $validator->errors()->add("data.{$key}", "The field [{$key}] is not defined for this content type.");
            }
        }

        try {
            app(SchemaValidator::class)->validate($type, $data);
        } catch (InvalidContentData $e) {
            $validator->errors()->add("data.{$e->field}", $e->getMessage());
        }
    }
}

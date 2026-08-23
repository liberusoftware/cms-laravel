<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Liberu\Cms\ContentTypes\Http\Requests\Concerns\ValidatesEntryData;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a Content-Entry create request on the Delivery API. The entry's
 * `data` is validated against the selected content type's field schema.
 */
final class StoreContentEntryRequest extends FormRequest
{
    use ValidatesEntryData;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'content_type_id' => ['required', 'integer', 'exists:cms_content_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'data' => ['sometimes', 'nullable', 'array'],
            'status' => ['sometimes', Rule::enum(WorkflowState::class)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateEntryData($validator, ContentType::find($this->integer('content_type_id')));
        });
    }
}

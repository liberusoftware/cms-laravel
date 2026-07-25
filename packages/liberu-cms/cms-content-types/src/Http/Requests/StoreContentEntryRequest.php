<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a Content-Entry create request on the Delivery API. The entry's
 * field `data` is stored as-is; deep validation against the content type's
 * schema is a later increment.
 */
final class StoreContentEntryRequest extends FormRequest
{
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
}

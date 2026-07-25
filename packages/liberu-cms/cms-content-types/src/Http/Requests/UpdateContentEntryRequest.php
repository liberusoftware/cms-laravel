<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a partial Content-Entry update on the Delivery API.
 */
final class UpdateContentEntryRequest extends FormRequest
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
            'content_type_id' => ['sometimes', 'integer', 'exists:cms_content_types,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255'],
            'data' => ['sometimes', 'nullable', 'array'],
            'status' => ['sometimes', Rule::enum(WorkflowState::class)],
        ];
    }
}

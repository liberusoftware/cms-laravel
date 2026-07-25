<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a partial Page update on the Delivery API: every field is optional,
 * but any field present must be valid. A `status` change is applied through the
 * editorial workflow, not written directly.
 */
final class UpdatePageRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'nullable', 'string'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'template' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', Rule::enum(WorkflowState::class)],
            'parent_id' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}

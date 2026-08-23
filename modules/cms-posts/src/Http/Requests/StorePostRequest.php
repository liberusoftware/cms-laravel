<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a Post create request on the Delivery API. Category and tag ids that
 * do not belong to the tenant are dropped at sync time, so only shape is checked
 * here.
 */
final class StorePostRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'content' => ['sometimes', 'nullable', 'string'],
            'excerpt' => ['sometimes', 'nullable', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::enum(WorkflowState::class)],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['integer'],
        ];
    }
}

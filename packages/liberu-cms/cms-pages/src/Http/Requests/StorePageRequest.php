<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a Page create request on the Delivery API. Tenant ownership
 * (team_id) and authorship are never client-supplied — the tenant is stamped
 * from the request context — so they are absent here by design.
 */
final class StorePageRequest extends FormRequest
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
            'template' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', Rule::enum(WorkflowState::class)],
            'parent_id' => ['sometimes', 'nullable', 'integer'],
        ];
    }
}

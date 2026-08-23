<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Http\Requests\Concerns\ValidatesEntryData;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\Contracts\Content\WorkflowState;

/**
 * Validates a partial Content-Entry update on the Delivery API. When `data` is
 * supplied it fully replaces the stored payload, so it is validated against the
 * persisted entry's own type — a spoofed `content_type_id` cannot bypass rules.
 */
final class UpdateContentEntryRequest extends FormRequest
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
            'content_type_id' => ['sometimes', 'integer', 'exists:cms_content_types,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255'],
            'data' => ['sometimes', 'nullable', 'array'],
            'status' => ['sometimes', Rule::enum(WorkflowState::class)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty() || ! $this->has('data')) {
                return;
            }

            $entry = app(ContentEntryRepositoryInterface::class)->find((int) $this->route('id'));

            $this->validateEntryData($validator, $entry instanceof ContentEntry ? $entry->type : null);
        });
    }
}

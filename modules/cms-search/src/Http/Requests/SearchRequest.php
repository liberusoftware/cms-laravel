<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the Delivery API search query. A query shorter than the configured
 * minimum is rejected with a 422.
 */
final class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $min = config('cms-search.min_query_length', 2);
        $min = is_numeric($min) ? (int) $min : 2;

        return [
            'q' => ['required', 'string', 'min:'.$min],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'q.required' => 'A search query (q) is required.',
            'q.min' => 'The search query is too short.',
        ];
    }
}

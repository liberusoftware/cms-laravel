<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApi\Data;

use Illuminate\Validation\ValidationException;

final readonly class DeliveryQuery
{
    /**
     * @param  list<string>  $fields
     * @param  list<string>  $includes
     * @param  array<string, scalar>  $filters
     */
    private function __construct(public string $version, public array $fields, public array $includes, public array $filters, public int $page, public int $perPage, public ?string $locale, public bool $preview) {}

    /** @param array<string, mixed> $input */
    public static function from(array $input): self
    {
        $version = $input['version'] ?? 'v1';
        $locale = $input['locale'] ?? null;
        if (! is_string($version) || ! preg_match('/^v[1-9][0-9]*$/', $version) || ($locale !== null && (! is_string($locale) || ! preg_match('/^[a-z]{2}(?:-[A-Z]{2})?$/', $locale)))) {
            throw ValidationException::withMessages(['query' => 'The delivery query version or locale is invalid.']);
        }
        $fields = self::strings($input['fields'] ?? [], 'fields');
        $includes = self::strings($input['include'] ?? [], 'include');
        $filters = $input['filter'] ?? [];
        if (! is_array($filters)) {
            throw ValidationException::withMessages(['filter' => 'Filters must be an object.']);
        }
        foreach ($filters as $key => $value) {
            if (! is_string($key) || ! is_scalar($value)) {
                throw ValidationException::withMessages(['filter' => 'Filters must contain scalar values.']);
            }
        }
        $page = $input['page'] ?? 1;
        $perPage = $input['per_page'] ?? 20;
        if (! is_int($page) || ! is_int($perPage) || $page < 1 || $perPage < 1 || $perPage > 100) {
            throw ValidationException::withMessages(['pagination' => 'Pagination is invalid.']);
        }

        return new self($version, $fields, $includes, $filters, $page, $perPage, $locale, (bool) ($input['preview'] ?? false));
    }

    /** @return list<string> */
    private static function strings(mixed $value, string $field): array
    {
        if (! is_array($value)) {
            throw ValidationException::withMessages([$field => 'Values must be an array.']);
        }
        $result = [];
        foreach ($value as $item) {
            if (! is_string($item) || $item === '' || ! preg_match('/^[A-Za-z0-9_.-]+$/', $item)) {
                throw ValidationException::withMessages([$field => 'Values contain an invalid entry.']);
            } $result[] = $item;
        }

        return array_values(array_unique($result));
    }
}

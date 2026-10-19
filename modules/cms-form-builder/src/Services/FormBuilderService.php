<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilder\Services;

use Illuminate\Validation\ValidationException;

final class FormBuilderService
{
    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function validate(array $steps, array $input): array
    {
        $rules = [];
        foreach ($this->visibleFields($steps, $input) as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'string';
            if (! is_string($name) || ! is_string($type)) {
                continue;
            }
            $rules[$name] = [($field['required'] ?? false) === true ? 'required' : 'nullable', $this->rule($type)];
        }

        $validated = validator($input, $rules)->validate();
        if (! is_array($validated)) {
            return [];
        }
        $result = [];
        foreach ($validated as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param  array<int, array<string, mixed>>  $steps
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, mixed>>
     */
    public function visibleFields(array $steps, array $input): array
    {
        $fields = [];
        foreach ($steps as $step) {
            $stepFields = $step['fields'] ?? [];
            if (! is_array($stepFields)) {
                continue;
            }
            foreach ($stepFields as $field) {
                if (! is_array($field)) {
                    continue;
                }
                $normalized = [];
                foreach ($field as $key => $value) {
                    if (is_string($key)) {
                        $normalized[$key] = $value;
                    }
                }
                if (! is_string($normalized['name'] ?? null)) {
                    continue;
                }
                $when = $normalized['when'] ?? null;
                if (is_array($when)) {
                    $dependsOn = $when['field'] ?? null;
                    $expected = $when['equals'] ?? null;
                    if (! is_string($dependsOn) || ($input[$dependsOn] ?? null) !== $expected) {
                        continue;
                    }
                }
                $fields[] = $normalized;
            }
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $calculations
     * @param  array<string, mixed>  $values
     * @return array<string, float|int>
     */
    public function calculate(array $calculations, array $values): array
    {
        $result = [];
        foreach ($calculations as $name => $definition) {
            if (! is_string($name) || ! is_array($definition)) {
                continue;
            }
            $fields = $definition['sum'] ?? [];
            if (! is_array($fields)) {
                continue;
            }
            $total = 0;
            foreach ($fields as $field) {
                if (is_string($field) && is_numeric($values[$field] ?? null)) {
                    $total += $values[$field];
                }
            }
            $result[$name] = $total;
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $confirmation
     * @return array{message: string, redirect: string|null}
     */
    public function confirmation(array $confirmation): array
    {
        $message = $confirmation['message'] ?? 'Your response has been received.';
        $redirect = $confirmation['redirect'] ?? null;
        if (! is_string($message) || strlen($message) > 1000 || ($redirect !== null && ! is_string($redirect))) {
            throw ValidationException::withMessages(['confirmation' => 'The confirmation configuration is invalid.']);
        }
        if (is_string($redirect) && (str_contains($redirect, "\n") || str_contains($redirect, "\r") || ! filter_var($redirect, FILTER_VALIDATE_URL))) {
            throw ValidationException::withMessages(['redirect' => 'The confirmation redirect must be an absolute URL.']);
        }

        return ['message' => $message, 'redirect' => $redirect];
    }

    public function embed(string $publicId, string $origin = 'https://example.invalid'): string
    {
        if (! preg_match('/^[0-9a-f-]{36}$/i', $publicId)) {
            throw ValidationException::withMessages(['public_id' => 'The form identifier is invalid.']);
        }
        if (! filter_var($origin, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['origin' => 'The embed origin is invalid.']);
        }

        return '<iframe loading="lazy" title="CMS form" src="'.htmlspecialchars(rtrim($origin, '/').'/forms/'.$publicId, ENT_QUOTES, 'UTF-8').'" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
    }

    private function rule(string $type): string
    {
        return match ($type) {
            'email' => 'email', 'integer' => 'integer', 'numeric' => 'numeric', 'url' => 'url', 'boolean' => 'boolean', 'date' => 'date', default => 'string'
        };
    }
}

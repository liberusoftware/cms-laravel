<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilderApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FormBuilder\Services\FormBuilderService;

final class FormBuilderController
{
    public function validateForm(Request $request, FormBuilderService $service): JsonResponse
    {
        $data = $request->validate(['steps' => ['required', 'array'], 'input' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_array($data['steps'] ?? null) || ! is_array($data['input'] ?? null)) {
            throw ValidationException::withMessages(['steps' => 'The form schema is invalid.']);
        }

        return response()->json(['data' => $service->validate($this->steps($data['steps']), $this->stringKeyed($data['input']))]);
    }

    public function visibleFields(Request $request, FormBuilderService $service): JsonResponse
    {
        $data = $request->validate(['steps' => ['required', 'array'], 'input' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_array($data['steps'] ?? null) || ! is_array($data['input'] ?? null)) {
            throw ValidationException::withMessages(['steps' => 'The form schema is invalid.']);
        }

        return response()->json(['data' => $service->visibleFields($this->steps($data['steps']), $this->stringKeyed($data['input']))]);
    }

    public function calculate(Request $request, FormBuilderService $service): JsonResponse
    {
        $data = $request->validate(['calculations' => ['required', 'array'], 'values' => ['sometimes', 'array']]);
        if (! is_array($data) || ! is_array($data['calculations'] ?? null) || ! is_array($data['values'] ?? null)) {
            throw ValidationException::withMessages(['calculations' => 'The calculation payload is invalid.']);
        }

        return response()->json(['data' => $service->calculate($this->stringKeyed($data['calculations']), $this->stringKeyed($data['values']))]);
    }

    public function confirmation(Request $request, FormBuilderService $service): JsonResponse
    {
        $data = $request->validate(['confirmation' => ['required', 'array']]);
        if (! is_array($data) || ! is_array($data['confirmation'] ?? null)) {
            throw ValidationException::withMessages(['confirmation' => 'The confirmation payload is invalid.']);
        }

        return response()->json(['data' => $service->confirmation($this->stringKeyed($data['confirmation']))]);
    }

    public function embed(string $publicId, Request $request, FormBuilderService $service): JsonResponse
    {
        $data = $request->validate(['origin' => ['sometimes', 'url']]);
        $origin = is_array($data) && is_string($data['origin'] ?? null) ? $data['origin'] : 'https://example.invalid';

        return response()->json(['data' => ['html' => $service->embed($publicId, $origin)]]);
    }

    /** @return array<int, array<string, mixed>> */
    private function steps(mixed $steps): array
    {
        $result = [];
        if (! is_array($steps)) {
            return $result;
        }
        foreach ($steps as $step) {
            if (! is_array($step)) {
                continue;
            }
            $result[] = $this->stringKeyed($step);
        }

        return $result;
    }

    /** @return array<string, mixed> */
    private function stringKeyed(mixed $value): array
    {
        $result = [];
        if (! is_array($value)) {
            return $result;
        }
        foreach ($value as $key => $item) {
            if (is_string($key)) {
                $result[$key] = $item;
            }
        }

        return $result;
    }
}

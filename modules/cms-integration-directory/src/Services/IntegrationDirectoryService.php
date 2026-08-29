<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectory\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\IntegrationDirectory\Models\Integration;

final class IntegrationDirectoryService
{
    public function register(string $key, string $name, string $provider, array $configuration = [], ?int $teamId = null): Integration
    {
        $this->validateKey($key);
        if (trim($name) === '' || trim($provider) === '') {
            throw ValidationException::withMessages(['integration' => 'Integration name and provider are required.']);
        }

        return Integration::query()->updateOrCreate(['key' => $key], ['name' => trim($name), 'provider' => trim($provider), 'configuration' => $configuration, 'status' => 'disabled', 'team_id' => $teamId]);
    }

    public function enable(Integration $integration): Integration
    {
        $integration->update(['status' => 'enabled']);

        return $integration->refresh();
    }

    public function disable(Integration $integration): Integration
    {
        $integration->update(['status' => 'disabled']);

        return $integration->refresh();
    }

    public function health(Integration $integration, string $status, ?string $message = null): Integration
    {
        if (! in_array($status, ['healthy', 'degraded', 'unhealthy', 'unknown'], true)) {
            throw ValidationException::withMessages(['health_status' => 'Invalid integration health status.']);
        }
        $integration->update(['health_status' => $status, 'health_message' => $message, 'last_checked_at' => now()]);

        return $integration->refresh();
    }

    /** @return array<int, Integration> */
    public function enabled(?string $category = null): array
    {
        return Integration::query()->where('status', 'enabled')->when($category, fn ($query) => $query->where('category', $category))->orderBy('name')->get()->all();
    }

    private function validateKey(string $key): void
    {
        if (preg_match('/\\A[a-z][a-z0-9_.-]{1,79}\\z/', $key) !== 1) {
            throw ValidationException::withMessages(['key' => 'Integration keys must be lowercase kebab-case identifiers.']);
        }
    }
}

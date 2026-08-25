<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagement\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ConfigurationManagement\Models\ConfigurationRelease;

final readonly class ConfigurationService
{
    public function __construct(private DatabaseManager $db) {}

    public function releases(?int $teamId, string $environment, int $perPage = 25): LengthAwarePaginator
    {
        return ConfigurationRelease::query()->where('environment', $environment)->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->latest()->paginate(max(1, min($perPage, (int) config('configuration-management.pagination.max', 100))));
    }

    public function export(array $payload, string $version, string $environment, ?int $actorId = null, ?int $teamId = null, array $dependencies = []): ConfigurationRelease
    {
        if ($version === '' || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,79}$/', $version)) {
            throw ValidationException::withMessages(['version' => 'Version must be a stable identifier.']);
        }
        if ($environment === '') {
            throw ValidationException::withMessages(['environment' => 'Environment is required.']);
        }
        $safe = $this->excludeSecrets($payload);
        $encoded = json_encode($safe, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return ConfigurationRelease::query()->updateOrCreate(['team_id' => $teamId, 'environment' => $environment, 'version' => $version], ['payload' => $safe, 'dependencies' => $dependencies, 'checksum' => hash('sha256', $encoded), 'status' => 'draft', 'created_by' => $actorId]);
    }

    public function compare(ConfigurationRelease $from, ConfigurationRelease $to): array
    {
        $changes = [];
        $keys = array_unique([...array_keys($from->payload ?? []), ...array_keys($to->payload ?? [])]);
        foreach ($keys as $key) {
            if (($from->payload[$key] ?? null) !== ($to->payload[$key] ?? null)) {
                $changes[] = ['path' => (string) $key, 'from' => $from->payload[$key] ?? null, 'to' => $to->payload[$key] ?? null];
            }
        }

        return ['from' => $from->version, 'to' => $to->version, 'changes' => $changes];
    }

    public function validateDependencies(ConfigurationRelease $release, array $available): array
    {
        $missing = array_values(array_diff($release->dependencies ?? [], $available));

        return ['valid' => $missing === [], 'missing' => $missing];
    }

    public function promote(ConfigurationRelease $release, array $available = []): ConfigurationRelease
    {
        $dependency = $this->validateDependencies($release, $available);
        if (! $dependency['valid']) {
            throw ValidationException::withMessages(['dependencies' => 'Required dependencies are unavailable: '.implode(', ', $dependency['missing'])]);
        }
        $this->db->transaction(function () use ($release): void {
            ConfigurationRelease::query()->where('team_id', $release->team_id)->where('environment', $release->environment)->where('status', 'promoted')->update(['status' => 'superseded']);
            $release->update(['status' => 'promoted', 'promoted_at' => now()]);
        });

        return $release->fresh();
    }

    public function rollback(ConfigurationRelease $release): ConfigurationRelease
    {
        if ($release->status !== 'promoted') {
            throw ValidationException::withMessages(['release' => 'Only a promoted release can be rolled back.']);
        }
        $release->update(['status' => 'rolled_back', 'rolled_back_at' => now()]);

        return $release->fresh();
    }

    private function excludeSecrets(array $payload): array
    {
        $secretKeys = array_map(strtolower(...), config('configuration-management.secret_keys', []));
        $result = [];
        foreach ($payload as $key => $value) {
            $name = strtolower((string) $key);
            if (array_any($secretKeys, fn (string $secret): bool => str_contains($name, $secret))) {
                continue;
            }
            $result[$key] = is_array($value) ? $this->excludeSecrets($value) : $value;
        }

        return $result;
    }
}

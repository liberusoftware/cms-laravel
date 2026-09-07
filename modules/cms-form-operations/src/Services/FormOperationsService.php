<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperations\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\FormOperations\Models\OperationalSubmission;

final readonly class FormOperationsService
{
    public function __construct(private Encrypter $encrypter, private CacheRepository $cache) {}

    /** @param array<string, mixed> $payload */
    public function submit(int $formId, array $payload, string $clientFingerprint, bool $consented, ?int $teamId = null, int $retentionDays = 30, int $maxPerMinute = 10): OperationalSubmission
    {
        if ($formId < 1 || $clientFingerprint === '' || ! $consented || $retentionDays < 1 || $maxPerMinute < 1) {
            throw ValidationException::withMessages(['submission' => 'Submission requirements are invalid.']);
        }
        $clientHash = hash('sha256', $clientFingerprint);
        $key = 'cms-form-operations:'.$teamId.':'.$clientHash.':'.now()->format('YmdHi');
        $count = (int) $this->cache->increment($key);
        if ($count === 1) {
            $this->cache->put($key, $count, 60);
        }
        if ($count > $maxPerMinute) {
            throw ValidationException::withMessages(['submission' => 'Submission rate limit exceeded.']);
        }

        return OperationalSubmission::query()->create(['public_id' => (string) Str::uuid(), 'team_id' => $teamId, 'form_id' => $formId, 'encrypted_payload' => $this->encrypter->encrypt(json_encode($payload, JSON_THROW_ON_ERROR)), 'client_hash' => $clientHash, 'consented_at' => now(), 'retention_until' => now()->addDays($retentionDays), 'status' => 'received']);
    }

    /** @return array<string, mixed> */
    public function export(OperationalSubmission $submission, ?int $teamId = null): array
    {
        if ($submission->team_id !== $teamId) {
            throw ValidationException::withMessages(['team_id' => 'The submission belongs to another tenant.']);
        }
        $encryptedPayload = $submission->getAttribute('encrypted_payload');
        if (! is_string($encryptedPayload)) {
            throw ValidationException::withMessages(['submission' => 'The submission payload is invalid.']);
        }
        $decryptedPayload = $this->encrypter->decrypt($encryptedPayload);
        if (! is_string($decryptedPayload)) {
            throw ValidationException::withMessages(['submission' => 'The submission payload is invalid.']);
        }
        $decoded = json_decode($decryptedPayload, true, 512, JSON_THROW_ON_ERROR);
        if (! is_array($decoded) || array_is_list($decoded)) {
            throw ValidationException::withMessages(['submission' => 'The submission payload is invalid.']);
        }

        $payload = [];
        foreach ($decoded as $key => $value) {
            if (! is_string($key)) {
                throw ValidationException::withMessages(['submission' => 'The submission payload is invalid.']);
            }
            $payload[$key] = $value;
        }

        return $payload;
    }

    public function purgeExpired(?int $teamId = null): int
    {
        $deleted = OperationalSubmission::query()->where('team_id', $teamId)->whereNotNull('retention_until')->where('retention_until', '<', now())->delete();

        return is_int($deleted) ? $deleted : 0;
    }
}

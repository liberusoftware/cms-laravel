<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegration\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\AnalyticsIntegration\Contracts\AnalyticsAdapterInterface;
use Liberu\Cms\AnalyticsIntegration\Models\AnalyticsEvent;
use Liberu\Cms\AnalyticsIntegration\Models\AnalyticsMapping;

final class AnalyticsIntegrationService
{
    /** @var array<string, AnalyticsAdapterInterface> */
    private array $adapters = [];

    public function recordEvent(?int $teamId, array $data): AnalyticsEvent
    {
        $type = (string) ($data['event_type'] ?? '');
        $name = trim((string) ($data['event_name'] ?? ''));
        $key = trim((string) ($data['idempotency_key'] ?? ''));
        if (! in_array($type, ['content', 'view', 'conversion'], true)) {
            throw ValidationException::withMessages(['event_type' => 'Event type must be content, view, or conversion.']);
        }
        if ($name === '' || strlen($name) > 120) {
            throw ValidationException::withMessages(['event_name' => 'A valid event name is required.']);
        }
        if ($key === '' || strlen($key) > 255) {
            throw ValidationException::withMessages(['idempotency_key' => 'An idempotency key is required.']);
        }
        $consent = (bool) ($data['consent_granted'] ?? false);
        $payload = $consent ? ($data['payload'] ?? []) : [];
        if (! is_array($payload)) {
            throw ValidationException::withMessages(['payload' => 'Event payload must be an object.']);
        }

        return AnalyticsEvent::query()->firstOrCreate(['team_id' => $teamId, 'idempotency_key' => $key], ['event_type' => $type, 'event_name' => $name, 'subject_type' => $data['subject_type'] ?? null, 'subject_id' => isset($data['subject_id']) ? (string) $data['subject_id'] : null, 'consent_category' => $data['consent_category'] ?? 'analytics', 'consent_granted' => $consent, 'status' => $consent ? 'accepted' : 'suppressed', 'payload' => $payload, 'occurred_at' => $data['occurred_at'] ?? now()]);
    }

    public function saveMapping(?int $teamId, array $data): AnalyticsMapping
    {
        $type = (string) ($data['event_type'] ?? '');
        $provider = trim((string) ($data['provider'] ?? ''));
        $measurement = trim((string) ($data['measurement_key'] ?? ''));
        if (! in_array($type, ['content', 'view', 'conversion'], true) || $provider === '' || $measurement === '') {
            throw ValidationException::withMessages(['mapping' => 'Event type, provider, and measurement key are required.']);
        }

        return AnalyticsMapping::query()->updateOrCreate(['team_id' => $teamId, 'event_type' => $type, 'provider' => $provider], ['measurement_key' => $measurement, 'consent_category' => $data['consent_category'] ?? 'analytics', 'config' => $data['config'] ?? [], 'enabled' => (bool) ($data['enabled'] ?? true)]);
    }

    /** @return array{total:int,by_type:array<string,int>,accepted:int,suppressed:int} */
    public function dashboard(?int $teamId, ?string $from = null, ?string $to = null): array
    {
        $query = AnalyticsEvent::query()->where('team_id', $teamId)->when($from, fn ($q) => $q->where('occurred_at', '>=', $from))->when($to, fn ($q) => $q->where('occurred_at', '<=', $to));
        $rows = $query->get(['event_type', 'status']);

        return ['total' => $rows->count(), 'by_type' => $rows->countBy('event_type')->all(), 'accepted' => $rows->where('status', 'accepted')->count(), 'suppressed' => $rows->where('status', 'suppressed')->count()];
    }

    public function registerAdapter(AnalyticsAdapterInterface $adapter): void
    {
        $this->adapters[$adapter->key()] = $adapter;
    }

    /** @return array<string, mixed> */
    public function adapterPayload(string $provider, AnalyticsEvent $event, AnalyticsMapping $mapping): array
    {
        if (! isset($this->adapters[$provider])) {
            throw ValidationException::withMessages(['provider' => 'The analytics adapter is not registered.']);
        }
        if (! $event->consent_granted || ! $mapping->enabled) {
            return [];
        }

        return $this->adapters[$provider]->payload($event, ['measurement_key' => $mapping->measurement_key, ...($mapping->config ?? [])]);
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Audit\Models\AuditLog;

/** @mixin AuditLog */
final class AuditLogResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var AuditLog $log */
        $log = $this->resource;

        return [
            'id' => (string) $log->id,
            'type' => 'cms-audit-and-history',
            'attributes' => [
                'action' => $log->action,
                'actor_id' => $log->actor_id,
                'actor_label' => $log->actor_label,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'team_id' => $log->team_id,
                'ip_address' => $log->ip_address,
                'metadata' => $log->metadata ?? [],
                'created_at' => $log->created_at?->toISOString(),
            ],
        ];
    }
}

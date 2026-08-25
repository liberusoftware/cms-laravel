<?php

declare(strict_types=1);

namespace Liberu\Cms\Audit;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Liberu\Cms\Audit\Models\AuditLog;

/**
 * Writes append-only audit records, resolving the request context (actor, team,
 * IP) so callers only supply what the event itself carries. Actor identity is
 * snapshotted (id + a human label) so a record still names its actor after that
 * user is deleted.
 */
final readonly class AuditLogger
{
    public function __construct(
        private AuthFactory $auth,
        private Request $request,
    ) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        string $action,
        ?string $subjectType = null,
        int|string|null $subjectId = null,
        array $metadata = [],
        ?Authenticatable $actor = null,
        ?string $actorLabel = null,
    ): AuditLog {
        $actor ??= $this->auth->guard()->user();

        return AuditLog::query()->create([
            'action' => $action,
            'actor_id' => $this->actorId($actor),
            'actor_label' => $actorLabel ?? $this->actorLabel($actor),
            'subject_type' => $subjectType,
            'subject_id' => $subjectId === null ? null : (string) $subjectId,
            'team_id' => $this->teamId($actor),
            'ip_address' => $this->request->ip(),
            'metadata' => $metadata === [] ? null : $metadata,
        ]);
    }

    private function actorId(?Authenticatable $actor): ?string
    {
        if (! $actor instanceof Authenticatable) {
            return null;
        }

        $id = $actor->getAuthIdentifier();

        return is_scalar($id) ? (string) $id : null;
    }

    private function actorLabel(?Authenticatable $actor): ?string
    {
        if (! $actor instanceof Model) {
            return null;
        }

        foreach (['email', 'name'] as $attribute) {
            $value = $actor->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function teamId(?Authenticatable $actor): ?int
    {
        if (! $actor instanceof Model) {
            return null;
        }

        $teamId = $actor->getAttribute('current_team_id');

        return is_numeric($teamId) ? (int) $teamId : null;
    }
}

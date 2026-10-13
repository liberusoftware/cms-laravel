<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int|null $team_id
 * @property string $capability
 * @property string $prompt
 * @property array<string, mixed>|null $input
 * @property array<string, mixed>|null $result
 * @property string $status
 * @property string $idempotency_key
 * @property string|null $confirmation_hash
 * @property Carbon|null $confirmed_at
 * @property string|null $failure_reason
 */
final class CopilotRequest extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_copilot_requests';

    #[\Override]
    protected $fillable = ['team_id', 'capability', 'prompt', 'input', 'result', 'status', 'idempotency_key', 'confirmation_hash', 'confirmed_at', 'failure_reason'];

    protected function casts(): array
    {
        return ['input' => 'array', 'result' => 'array', 'confirmed_at' => 'datetime'];
    }
}

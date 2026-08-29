<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

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

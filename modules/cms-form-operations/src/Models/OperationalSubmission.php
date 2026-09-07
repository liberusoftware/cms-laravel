<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property string $public_id
 * @property int $form_id
 * @property int|null $team_id
 * @property string $encrypted_payload
 * @property string $client_hash
 * @property Carbon $consented_at
 * @property Carbon|null $retention_until
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class OperationalSubmission extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_form_operation_submissions';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['team_id' => 'integer', 'form_id' => 'integer', 'consented_at' => 'datetime', 'retention_until' => 'datetime'];
}

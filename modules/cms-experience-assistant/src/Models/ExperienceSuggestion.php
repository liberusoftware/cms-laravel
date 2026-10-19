<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistant\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Liberu\Cms\Core\Tenant\HasTenant;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $public_id
 * @property string $surface
 * @property array<string, mixed> $definition
 * @property array<string, mixed>|null $constraints
 * @property array<string, mixed>|null $diagnostics
 * @property string $status
 * @property string|null $reviewer_key
 * @property Carbon|null $approved_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class ExperienceSuggestion extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_experience_assistant_suggestions';

    #[\Override]
    protected $guarded = [];

    #[\Override]
    protected $casts = ['team_id' => 'integer', 'definition' => 'array', 'constraints' => 'array', 'diagnostics' => 'array', 'approved_at' => 'datetime'];
}

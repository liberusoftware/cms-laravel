<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class WordPressMigrationRecord extends Model
{
    use HasTenant;

    protected $table = 'cms_wordpress_migration_records';

    protected $fillable = ['migration_id', 'record_type', 'source_id', 'source_parent_id', 'status', 'payload', 'source_identifiers', 'failure_reason', 'processed_at', 'team_id'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'source_identifiers' => 'array', 'processed_at' => 'datetime'];
    }

    public function migration(): BelongsTo
    {
        return $this->belongsTo(WordPressMigration::class, 'migration_id');
    }
}

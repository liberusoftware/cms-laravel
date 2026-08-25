<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Core\Tenant\HasTenant;

final class WordPressMigration extends Model
{
    use HasTenant;

    protected $table = 'cms_wordpress_migrations';

    protected $fillable = ['public_id', 'source_url', 'status', 'total_records', 'processed_records', 'failed_records', 'options', 'metadata', 'failure_reason', 'started_at', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['options' => 'array', 'metadata' => 'array', 'started_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function records(): HasMany
    {
        return $this->hasMany(WordPressMigrationRecord::class, 'migration_id');
    }
}

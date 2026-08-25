<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TranslationJob extends Model
{
    use HasTenant;

    protected $table = 'cms_translation_jobs';

    protected $fillable = ['public_id', 'external_key', 'name', 'source_locale', 'target_locale', 'status', 'vendor_key', 'total_units', 'completed_units', 'estimated_cost', 'actual_cost', 'currency', 'metadata', 'queued_at', 'completed_at', 'team_id'];

    protected function casts(): array
    {
        return ['estimated_cost' => 'float', 'actual_cost' => 'float', 'metadata' => 'array', 'queued_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        self::creating(function (self $job): void {
            $job->public_id ??= (string) Str::uuid();
            $job->currency ??= config('translation-management.cost_currency', 'USD');
        });
    }

    public function sourceChanges(): HasMany
    {
        return $this->hasMany(TranslationSourceChange::class, 'job_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TranslationAssignment::class, 'job_id');
    }
}

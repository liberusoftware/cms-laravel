<?php

declare(strict_types=1);

namespace Liberu\Cms\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Cms\Core\Tenant\HasTenant;

final class TermAssignment extends Model
{
    use HasTenant;

    #[\Override]
    public $timestamps = false;

    #[\Override]
    protected $table = 'cms_taxonomy_assignments';

    #[\Override]
    protected $fillable = ['term_id', 'subject_type', 'subject_id', 'team_id'];

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}

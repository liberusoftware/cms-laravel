<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContent\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class EditorialSeries extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_editorial_series';

    #[\Override]
    protected $fillable = ['team_id', 'public_id', 'name', 'description'];
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class PageRedirect extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_page_redirects';

    #[\Override]
    protected $fillable = ['from_path', 'to_path', 'status_code', 'active', 'team_id'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'status_code' => 'integer'];
    }
}

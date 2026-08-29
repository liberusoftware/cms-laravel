<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ContactLocation extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_contact_locations';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'address', 'city', 'country'];
}

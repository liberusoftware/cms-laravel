<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactLocation extends Model
{
    protected $table = 'cms_contact_locations';

    protected $fillable = ['team_id', 'name', 'address', 'city', 'country'];
}

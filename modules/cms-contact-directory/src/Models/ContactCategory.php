<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactCategory extends Model
{
    protected $table = 'cms_contact_categories';

    protected $fillable = ['team_id', 'name', 'slug'];
}

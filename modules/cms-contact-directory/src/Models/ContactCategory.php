<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactCategory extends Model
{
    #[\Override]
    protected $table = 'cms_contact_categories';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'slug'];
}

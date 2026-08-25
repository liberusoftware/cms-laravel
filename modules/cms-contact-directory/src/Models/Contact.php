<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Models;

use Illuminate\Database\Eloquent\Model;

final class Contact extends Model
{
    #[\Override]
    protected $table = 'cms_contacts';

    #[\Override]
    protected $fillable = ['team_id', 'category_id', 'location_id', 'name', 'department', 'email', 'phone', 'details', 'is_public'];

    protected function casts(): array
    {
        return ['details' => 'array', 'is_public' => 'boolean'];
    }
}

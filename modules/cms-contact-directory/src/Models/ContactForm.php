<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactForm extends Model
{
    #[\Override]
    protected $table = 'cms_contact_forms';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'schema', 'is_active'];

    protected function casts(): array
    {
        return ['schema' => 'array', 'is_active' => 'boolean'];
    }
}

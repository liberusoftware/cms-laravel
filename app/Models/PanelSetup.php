<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Application presentation progress; CMS site data remains owned by CMS Core.
 *
 * @property array<string, string|null> $data
 */
class PanelSetup extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['data' => 'array', 'completed_at' => 'datetime'];
    }
}

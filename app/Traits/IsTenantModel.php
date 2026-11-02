<?php

namespace App\Traits;

use Liberu\Foundation\Tenancy\Concerns\BelongsToTeam;

trait IsTenantModel
{
    use BelongsToTeam;
}

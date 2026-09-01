<?php

namespace Liberu\Foundation\OrganizationsFilament\Resources\TeamResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\Foundation\OrganizationsFilament\Resources\TeamResource;

class CreateTeam extends CreateRecord
{
    #[\Override]
    protected static string $resource = TeamResource::class;
}

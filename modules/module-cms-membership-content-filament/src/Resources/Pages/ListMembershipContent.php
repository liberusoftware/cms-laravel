<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\MembershipContentFilament\Resources\MembershipContentResource;

final class ListMembershipContent extends ListRecords
{
    #[\Override]
    protected static string $resource = MembershipContentResource::class;
}

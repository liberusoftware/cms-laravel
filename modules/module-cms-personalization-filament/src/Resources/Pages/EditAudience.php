<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Cms\PersonalizationFilament\Resources\AudienceResource;

final class EditAudience extends EditRecord
{
    #[\Override]
    protected static string $resource = AudienceResource::class;
}

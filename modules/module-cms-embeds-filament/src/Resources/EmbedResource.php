<?php

namespace Liberu\Cms\EmbedsFilament\Resources;

use Filament\Resources\Resource;
use Liberu\Cms\Embeds\Models\Embed;

class EmbedResource extends Resource
{
    protected static ?string $model = Embed::class;

    protected static ?string $slug = 'cms-embeds';

    public static function getPages(): array
    {
        return [];
    }
}

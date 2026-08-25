<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\VideoAndAudioFilament\Resources\MediaAssetResource;

final class ListMediaAssets extends ListRecords
{
    protected static string $resource = MediaAssetResource::class;
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\SyndicationAndFeedsFilament\Resources\FeedResource;

final class ListFeeds extends ListRecords
{
    protected static string $resource = FeedResource::class;
}

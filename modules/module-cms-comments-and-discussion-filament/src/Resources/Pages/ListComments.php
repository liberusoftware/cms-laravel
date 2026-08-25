<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussionFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\CommentsAndDiscussionFilament\Resources\CommentResource;

final class ListComments extends ListRecords
{
    #[\Override]
    protected static string $resource = CommentResource::class;
}

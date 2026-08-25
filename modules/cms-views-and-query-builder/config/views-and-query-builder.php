<?php

declare(strict_types=1);
use Liberu\Cms\Collections\Models\CollectionItem;
use Liberu\Cms\ContentTypes\Models\ContentEntry;

return [
    'pagination' => ['max' => 100],
    'sources' => [
        'content_entries' => ContentEntry::class,
        'collection_items' => CollectionItem::class,
    ],
    'allowed_operators' => ['=', '!=', '>', '>=', '<', '<=', 'like', 'in'],
    'allowed_visibility' => ['private', 'team', 'public'],
];

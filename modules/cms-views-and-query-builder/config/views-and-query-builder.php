<?php

declare(strict_types=1);

return [
    'pagination' => ['max' => 100],
    'sources' => [
        'content_entries' => 'Liberu\\Cms\\ContentTypes\\Models\\ContentEntry',
        'collection_items' => 'Liberu\\Cms\\Collections\\Models\\CollectionItem',
    ],
    'allowed_operators' => ['=', '!=', '>', '>=', '<', '<=', 'like', 'in'],
    'allowed_visibility' => ['private', 'team', 'public'],
];

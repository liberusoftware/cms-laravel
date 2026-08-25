<?php

declare(strict_types=1);

namespace Liberu\Cms\CommentsAndDiscussion;

use Liberu\Cms\Core\Module\AbstractModule;

final class CommentsAndDiscussionModule extends AbstractModule
{
    public function key(): string
    {
        return 'comments-and-discussion';
    }

    public function name(): string
    {
        return 'Comments and Discussion';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}

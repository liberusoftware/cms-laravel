<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Content;

/**
 * The editorial lifecycle a piece of content moves through (Part B §14):
 * Draft → Review → Published → Archived. Scheduling is expressed as the
 * Published state with a future publish date, so it needs no separate case.
 *
 * @api This enum is part of the public extension API.
 */
enum WorkflowState: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Archived = 'archived';

    /**
     * The states as value => human label pairs, for admin form selects.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $state) {
            $options[$state->value] = ucfirst($state->value);
        }

        return $options;
    }
}

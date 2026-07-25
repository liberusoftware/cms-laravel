<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Events\Form;

use Liberu\Cms\Contracts\Events\CmsEvent;

/**
 * Emitted when a public form submission is accepted and stored. Notification,
 * CRM, and automation modules listen for this to react to new submissions.
 */
final readonly class FormSubmitted implements CmsEvent
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public string $formSlug,
        public int|string $submissionId,
        public int|string|null $teamId,
        public array $data,
    ) {}

    public function name(): string
    {
        return 'forms.submitted';
    }
}

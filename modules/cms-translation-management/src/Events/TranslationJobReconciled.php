<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Events;

use Liberu\Cms\TranslationManagement\Models\TranslationJob;

final readonly class TranslationJobReconciled
{
    public function __construct(public TranslationJob $job) {}
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagement\Events;

use Liberu\Cms\TranslationManagement\Models\TranslationSourceChange;

final readonly class SourceChangeTranslated
{
    public function __construct(public TranslationSourceChange $sourceChange) {}
}

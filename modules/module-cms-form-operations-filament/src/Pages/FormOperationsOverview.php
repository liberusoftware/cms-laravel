<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsFilament\Pages;

use Filament\Pages\Page;

final class FormOperationsOverview extends Page
{
    #[\Override]
    protected string $view = 'cms-form-operations-filament::pages.form-operations-overview';

    #[\Override]
    protected static ?string $title = 'Form Operations';
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilderFilament\Pages;

use Filament\Pages\Page;

final class FormBuilderOverview extends Page
{
    #[\Override]
    protected string $view = 'cms-form-builder-filament::pages.form-builder-overview';

    #[\Override]
    protected static ?string $title = 'Form Builder';
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantFilament\Pages;

use Filament\Pages\Page;
use Liberu\Cms\AccessibilityAssistant\Services\AccessibilityAssistantService;

final class AccessibilityAnalyzerPage extends Page
{
    protected string $view = 'module-cms-accessibility-assistant-filament::accessibility-analyzer';

    protected static ?string $title = 'Accessibility Analyzer';

    public string $html = '';

    /** @var array<int, array{code:string, severity:string, message:string}> */
    public array $findings = [];

    public function analyze(AccessibilityAssistantService $service): void
    {
        $this->findings = $service->analyze($this->html);
    }
}

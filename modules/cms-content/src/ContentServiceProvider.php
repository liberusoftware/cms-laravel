<?php

declare(strict_types=1);

namespace Liberu\Cms\Content;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Content\Support\HtmlSanitizer;
use Liberu\Cms\Content\Workflow\Workflow;
use Liberu\Cms\Contracts\Content\WorkflowInterface;

/**
 * Content foundation provider. Not a module — this is always-on shared
 * infrastructure (like the kernel), so it registers unconditionally.
 */
final class ContentServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(WorkflowInterface::class, Workflow::class);
        $this->app->singleton(HtmlSanitizer::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // `@sanitize($html)` echoes author HTML with scripts/handlers stripped —
        // safe raw rendering for content output (OWASP A03).
        Blade::directive('sanitize', fn (string $expression): string => "<?php echo app(\\Liberu\\Cms\\Content\\Support\\HtmlSanitizer::class)->sanitize({$expression}); ?>");
    }
}

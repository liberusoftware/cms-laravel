<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Liberu\Cms\Contracts\Events\Content\ContentPublished;
use Liberu\Cms\Contracts\Events\Content\ContentStateChanged;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Events\Form\FormSubmitted;
use Liberu\Cms\Contracts\Events\Media\MediaUploaded;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Observability\Health\Checks\CacheHealthCheck;
use Liberu\Cms\Observability\Health\Checks\DatabaseHealthCheck;
use Liberu\Cms\Observability\Health\Checks\QueueHealthCheck;
use Liberu\Cms\Observability\Health\HealthCheckRegistry;
use Liberu\Cms\Observability\Metrics\LogMetricsRecorder;
use Liberu\Cms\Observability\Metrics\MetricsSubscriber;
use Liberu\Cms\Observability\Metrics\NullMetricsRecorder;

/**
 * Wires Observability: it binds the health-check registry before feature modules
 * register (so their `app()->bound(...)` guard passes) and the metrics recorder
 * seam, then — only while enabled — registers its own infra checks, the public
 * readiness route with its per-IP throttle, and the zero-coupling domain-event
 * metric counters.
 */
final class CmsObservabilityServiceProvider extends ModuleServiceProvider
{
    private const string RATE_LIMITER = 'cms-observability';

    public function module(): ModuleInterface
    {
        return new CmsObservabilityModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/observability.php', 'cms-observability');
        $this->registerMetricsChannel();

        $this->app->singleton(HealthCheckRegistryInterface::class, HealthCheckRegistry::class);

        $this->app->singleton(MetricsRecorderInterface::class, function (Application $app): MetricsRecorderInterface {
            if (! (bool) config('cms-observability.metrics.enabled', true)) {
                return new NullMetricsRecorder;
            }

            return new LogMetricsRecorder($app->make('log')->channel($this->metricsChannel()));
        });
    }

    protected function bootModule(): void
    {
        $this->registerHealthChecks($this->app->make(HealthCheckRegistryInterface::class));
        $this->configureRateLimiting();
        $this->loadModuleRoutesFrom(__DIR__.'/../routes/web.php');
        $this->subscribeMetrics();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/observability.php' => $this->app->configPath('cms-observability.php'),
            ], 'cms-observability-config');
        }
    }

    /**
     * Give metrics their own isolated log channel so they never pollute the app
     * log. Defined at runtime (not by editing the host's config/logging.php),
     * keeping the module removable; an operator-defined channel wins.
     */
    private function registerMetricsChannel(): void
    {
        $config = $this->app->make('config');
        $channel = $this->metricsChannel();

        if ($config->get("logging.channels.{$channel}") === null) {
            $config->set("logging.channels.{$channel}", [
                'driver' => 'single',
                'path' => $this->app->storagePath("logs/{$channel}.log"),
                'level' => 'debug',
            ]);
        }
    }

    /**
     * The isolated channel metrics are written to, falling back to the default
     * when misconfigured.
     */
    private function metricsChannel(): string
    {
        $channel = config('cms-observability.metrics.channel', 'cms-metrics');

        return is_string($channel) ? $channel : 'cms-metrics';
    }

    private function registerHealthChecks(HealthCheckRegistryInterface $registry): void
    {
        /** @var array<string, bool> $critical */
        $critical = (array) config('cms-observability.readiness.critical', []);

        $registry->register(new DatabaseHealthCheck(
            $this->app->make(ConnectionResolverInterface::class),
            (bool) ($critical['database'] ?? true),
        ));
        $registry->register(new CacheHealthCheck(
            $this->app->make(CacheFactory::class),
            (bool) ($critical['cache'] ?? false),
        ));
        $registry->register(new QueueHealthCheck(
            $this->app->make(QueueFactory::class),
            (bool) ($critical['queue'] ?? false),
        ));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for(self::RATE_LIMITER, function (Request $request): Limit {
            $configured = config('cms-observability.readiness.throttle', 60);
            $perMinute = is_numeric($configured) ? (int) $configured : 60;

            return Limit::perMinute($perMinute)->by(self::RATE_LIMITER.':'.($request->ip() ?? 'unknown'));
        });
    }

    /**
     * Increment a domain counter for each observed event. Guarded on the
     * recorder binding so the listener stays inert if the seam is ever unbound.
     */
    private function subscribeMetrics(): void
    {
        if (! $this->app->bound(MetricsRecorderInterface::class)) {
            return;
        }

        $bus = $this->app->make(EventBusInterface::class);
        $subscriber = fn (): MetricsSubscriber => $this->app->make(MetricsSubscriber::class);

        $bus->listen(ContentPublished::class, fn (ContentPublished $event) => $subscriber()->handleContentPublished($event));
        $bus->listen(ContentStateChanged::class, fn (ContentStateChanged $event) => $subscriber()->handleContentStateChanged($event));
        $bus->listen(FormSubmitted::class, fn (FormSubmitted $event) => $subscriber()->handleFormSubmitted($event));
        $bus->listen(MediaUploaded::class, fn (MediaUploaded $event) => $subscriber()->handleMediaUploaded($event));
    }
}

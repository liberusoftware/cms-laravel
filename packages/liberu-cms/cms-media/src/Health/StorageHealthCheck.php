<?php

declare(strict_types=1);

namespace Liberu\Cms\Media\Health;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Throwable;

/**
 * Readiness probe for the media disk: writes and deletes a tiny sentinel file to
 * prove storage is actually writable, not merely configured. Degraded, not
 * critical — the app can still serve content while uploads are unavailable.
 *
 * Contributed to the observability readiness registry through the contract; the
 * Media module imports nothing from cms-observability.
 */
final readonly class StorageHealthCheck implements HealthCheckInterface
{
    private const string PROBE_PREFIX = '__cms_readiness_probe_';

    public function __construct(
        private FilesystemFactory $filesystem,
        private string $disk,
        private bool $critical,
    ) {}

    public function name(): string
    {
        return 'storage';
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function check(): bool
    {
        $probe = self::PROBE_PREFIX.uniqid().'.txt';

        try {
            $disk = $this->filesystem->disk($this->disk);
            $disk->put($probe, 'ok');
            $disk->delete($probe);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}

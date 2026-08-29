<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigration\Services;

use Liberu\Cms\MigrationFramework\Models\MigrationJob;
use Liberu\Cms\MigrationFramework\Models\MigrationRecord;
use Liberu\Cms\MigrationFramework\Services\MigrationFrameworkService;

final readonly class DrupalMigrationService
{
    public function __construct(private MigrationFrameworkService $framework) {}

    public function start(string $source, array $options = [], ?int $teamId = null): MigrationJob
    {
        return $this->framework->start('drupal', ['source' => $source, ...$options], $teamId);
    }

    public function add(MigrationJob $job, string $type, string $sourceId, array $payload = []): MigrationRecord
    {
        return $this->framework->add($job, $type, $sourceId, $payload);
    }

    public function process(MigrationRecord $record, bool $success, ?string $reason = null): MigrationRecord
    {
        return $this->framework->process($record, $success, $reason);
    }

    public function complete(MigrationJob $job): MigrationJob
    {
        return $this->framework->complete($job);
    }
}

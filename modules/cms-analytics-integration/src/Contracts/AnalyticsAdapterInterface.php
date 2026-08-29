<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegration\Contracts;

use Liberu\Cms\AnalyticsIntegration\Models\AnalyticsEvent;

interface AnalyticsAdapterInterface
{
    public function key(): string;

    /** @return array<string, mixed> */
    public function payload(AnalyticsEvent $event, array $mapping): array;
}

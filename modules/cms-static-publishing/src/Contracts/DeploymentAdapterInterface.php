<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing\Contracts;

use Liberu\Cms\StaticPublishing\Models\StaticBuild;

interface DeploymentAdapterInterface
{
    public function key(): string;

    /** @return array<string, mixed> */
    public function deploy(StaticBuild $build): array;
}

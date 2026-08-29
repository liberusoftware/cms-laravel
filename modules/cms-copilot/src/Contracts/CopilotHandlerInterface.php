<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot\Contracts;

interface CopilotHandlerInterface
{
    public function capability(): string;

    /** @return array<string, mixed> */
    public function handle(string $prompt, array $input): array;
}

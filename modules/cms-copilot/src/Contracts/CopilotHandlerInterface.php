<?php

declare(strict_types=1);

namespace Liberu\Cms\Copilot\Contracts;

interface CopilotHandlerInterface
{
    public function capability(): string;

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function handle(string $prompt, array $input): array;
}

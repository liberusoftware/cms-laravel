<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegration\Contracts;

interface ForumProvider
{
    /** @return list<array<string, scalar|null>> */
    public function recentDiscussions(?int $teamId = null, int $limit = 10): array;

    public function moderationUrl(string $externalType, string $externalId): ?string;

    /** @return array<string, scalar|null> */
    public function ssoContext(string $subjectKey, ?int $teamId = null): array;
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Console;

use Illuminate\Console\Command;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Liberu\Cms\Contracts\Tenancy\TenantModelResolverInterface;

/**
 * Mints a Delivery token for a Team and prints it once. Onboards an API consumer
 * from the command line before any token-management UI exists. Revoke a token by
 * deleting the corresponding personal access token row (see the README).
 */
final class IssueTokenCommand extends Command
{
    protected $signature = 'cms-api:issue-token {team : The Team (tenant) id to issue a Delivery token for}
        {--name=delivery : A human-readable label for the token}
        {--write : Also grant write access (content:write) in addition to read}';

    protected $description = 'Issue a Delivery API token for a Team (read-only by default).';

    public function handle(TenantModelResolverInterface $resolver): int
    {
        $model = $resolver->tenantModel();

        if ($model === null) {
            $this->error('Multi-tenancy is disabled; there is no Team model to issue a Delivery token for.');

            return self::FAILURE;
        }

        $teamId = $this->argument('team');
        $team = $model::query()->find($teamId);

        if (! $team instanceof HasApiTokens) {
            $this->error(sprintf('No Team found with id [%s].', is_scalar($teamId) ? (string) $teamId : ''));

            return self::FAILURE;
        }

        $abilities = ['content:read'];

        if ($this->option('write')) {
            $abilities[] = 'content:write';
        }

        $token = $team->createToken((string) $this->option('name'), $abilities);

        $this->info('Delivery token issued. Store it now — it will not be shown again:');
        $this->line($token->plainTextToken);

        return self::SUCCESS;
    }
}

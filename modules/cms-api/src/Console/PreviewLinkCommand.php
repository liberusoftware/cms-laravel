<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Api\Support\PreviewLink;
use Liberu\Cms\Contracts\Preview\PreviewableSourceInterface;
use Liberu\Cms\Contracts\Preview\PreviewRegistryInterface;

/**
 * Mints a signed, expiring preview link for a single draft-inclusive content
 * item and prints it. Share the link to let a reviewer see unpublished content
 * without a login; it expires automatically.
 */
final class PreviewLinkCommand extends Command
{
    #[\Override]
    protected $signature = 'cms-api:preview-link {type : The content type key (e.g. pages, posts, content-entries)}
        {id : The item id}
        {--ttl= : Minutes until the link expires (defaults to config)}';

    #[\Override]
    protected $description = 'Mint a signed preview link for a single unpublished content item.';

    public function handle(PreviewRegistryInterface $registry, PreviewLink $links): int
    {
        $type = (string) $this->argument('type');
        $source = $registry->source($type);

        if (! $source instanceof PreviewableSourceInterface) {
            $this->error(sprintf(
                'No previewable content type [%s]. Known types: %s.',
                $type,
                implode(', ', array_keys($registry->sources())) ?: '(none)',
            ));

            return self::FAILURE;
        }

        $id = (int) $this->argument('id');
        $model = $source->find($id);

        if (! $model instanceof Model) {
            $this->error(sprintf('No [%s] item found with id [%d].', $type, $id));

            return self::FAILURE;
        }

        $team = $model->getAttribute('team_id');
        $ttl = $this->option('ttl');

        $url = $links->for(
            $type,
            $id,
            is_int($team) || is_string($team) ? $team : null,
            is_numeric($ttl) ? (int) $ttl : null,
        );

        $this->info('Preview link (expires automatically):');
        $this->line($url);

        return self::SUCCESS;
    }
}

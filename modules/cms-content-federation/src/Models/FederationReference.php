<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/** @property Carbon|null $cached_until */
final class FederationReference extends Model
{
    #[\Override]
    protected $table = 'cms_federation_references';

    #[\Override]
    protected $fillable = ['source_id', 'external_type', 'external_key', 'payload', 'etag', 'cached_until', 'last_fetched_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'cached_until' => 'datetime', 'last_fetched_at' => 'datetime'];
    }
}

<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Models;

use Illuminate\Database\Eloquent\Model;

final class SearchAnalytic extends Model
{
    #[\Override]
    protected $table = 'cms_search_analytics';

    #[\Override]
    protected $fillable = ['team_id', 'query', 'result_count', 'duration_ms', 'source'];
}

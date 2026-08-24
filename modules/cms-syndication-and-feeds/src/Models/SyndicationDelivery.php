<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeeds\Models;

use Illuminate\Database\Eloquent\Model;

final class SyndicationDelivery extends Model
{
    protected $table = 'cms_syndication_deliveries';

    protected $fillable = ['feed_id', 'destination', 'status', 'response', 'delivered_at'];

    protected function casts(): array
    {
        return ['response' => 'array', 'delivered_at' => 'datetime'];
    }
}

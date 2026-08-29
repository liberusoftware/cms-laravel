<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;

final class ExtensionSupport extends Model
{
    protected $table = 'cms_extension_support';

    protected $fillable = ['listing_id', 'channel', 'url', 'response_hours'];
}

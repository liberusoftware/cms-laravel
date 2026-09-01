<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;

final class ExtensionTrial extends Model
{
    #[\Override]
    protected $table = 'cms_extension_trials';

    #[\Override]
    protected $fillable = ['listing_id', 'subject_type', 'subject_id', 'status', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime'];
    }
}

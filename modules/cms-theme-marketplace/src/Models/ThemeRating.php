<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ThemeRating extends Model
{
    use HasTenant;

    #[\Override]
    protected $table = 'cms_theme_ratings';

    #[\Override]
    protected $fillable = ['theme_id', 'reviewer_type', 'reviewer_id', 'rating', 'review', 'team_id'];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }
}

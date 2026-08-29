<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Liberu\Cms\Core\Tenant\HasTenant;

final class ExtensionReview extends Model
{
    use HasTenant;

    protected $table = 'cms_extension_reviews';

    protected $fillable = ['listing_id', 'reviewer_type', 'reviewer_id', 'rating', 'review', 'status', 'team_id'];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }
}

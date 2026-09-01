<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModes\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $team_id
 * @property string $name
 * @property string $slug
 * @property string $content_type
 * @property string $mode_type
 * @property array<string, mixed>|null $formatters
 * @property array<string, mixed>|null $configuration
 * @property array<string, mixed>|null $responsive_variants
 * @property array<string, mixed>|null $projection
 * @property bool $active
 */
final class DisplayMode extends Model
{
    #[\Override]
    protected $table = 'cms_display_modes';

    #[\Override]
    protected $fillable = ['team_id', 'name', 'slug', 'content_type', 'mode_type', 'formatters', 'configuration', 'responsive_variants', 'projection', 'active'];

    protected function casts(): array
    {
        return ['formatters' => 'array', 'configuration' => 'array', 'responsive_variants' => 'array', 'projection' => 'array', 'active' => 'boolean'];
    }
}

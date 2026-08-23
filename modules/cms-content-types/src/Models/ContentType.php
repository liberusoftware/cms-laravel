<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\ContentTypes\Database\Factories\ContentTypeFactory;
use Liberu\Cms\ContentTypes\Fields\FieldDefinition;

/**
 * A user-defined content type (portfolio item, product, case study, …) whose
 * shape is a JSON field schema.
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string $singular_label
 * @property string $plural_label
 * @property array<int, array<string, mixed>>|null $fields
 */
final class ContentType extends Model
{
    /** @use HasFactory<ContentTypeFactory> */
    use HasFactory;

    #[\Override]
    protected $table = 'cms_content_types';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['key', 'name', 'singular_label', 'plural_label', 'fields'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['fields' => 'array'];
    }

    /**
     * The schema as value objects.
     *
     * @return array<int, FieldDefinition>
     */
    public function fieldDefinitions(): array
    {
        return array_map(
            FieldDefinition::fromArray(...),
            $this->fields ?? [],
        );
    }

    /**
     * @return HasMany<ContentEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(ContentEntry::class);
    }

    protected static function newFactory(): ContentTypeFactory
    {
        return ContentTypeFactory::new();
    }
}

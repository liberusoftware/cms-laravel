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
    protected $fillable = ['key', 'name', 'singular_label', 'plural_label', 'fields', 'schema_version', 'schema_history'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['fields' => 'array', 'schema_version' => 'integer', 'schema_history' => 'array'];
    }

    protected static function booted(): void
    {
        self::saving(function (ContentType $type): void {
            if (! $type->exists || ! $type->isDirty('fields')) {
                return;
            }

            $history = is_array($type->schema_history) ? $type->schema_history : [];
            $history[] = [
                'version' => ((int) ($type->schema_version ?? 1)) + 1,
                'fields' => $type->getOriginal('fields') ?? [],
                'reason' => null,
                'migrated_at' => now()->toAtomString(),
            ];

            $type->schema_version = count($history) + 1;
            $type->schema_history = $history;
        });
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
     * Replace the field schema while retaining an immutable migration trail.
     *
     * @param  array<int, array<string, mixed>>  $fields
     */
    public function migrateSchema(array $fields, ?string $reason = null): self
    {
        $this->fields = $fields;
        $this->save();

        if ($reason !== null && is_array($this->schema_history)) {
            $history = $this->schema_history;
            $history[array_key_last($history)]['reason'] = $reason;
            $this->schema_history = $history;
            $this->saveQuietly();
        }

        return $this;
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

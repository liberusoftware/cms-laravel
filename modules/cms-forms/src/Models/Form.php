<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Cms\Content\Support\Slugger;
use Liberu\Cms\Core\Tenant\HasTenant;
use Liberu\Cms\Forms\Database\Factories\FormFactory;
use Liberu\Cms\Forms\Fields\FormField;

/**
 * A public form definition: a named, slugged set of fields that visitors submit.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property array<int, array<string, mixed>>|null $fields
 * @property int|null $team_id
 */
final class Form extends Model
{
    /** @use HasFactory<FormFactory> */
    use HasFactory;

    use HasTenant;

    #[\Override]
    protected $table = 'cms_forms';

    /**
     * @var list<string>
     */
    #[\Override]
    protected $fillable = ['name', 'slug', 'fields', 'team_id'];

    /**
     * @return array<string, string>
     */
    #[\Override]
    protected function casts(): array
    {
        return ['fields' => 'array'];
    }

    #[\Override]
    protected static function booted(): void
    {
        self::saving(function (Form $form): void {
            if (blank($form->slug) && filled($form->name)) {
                $form->slug = Slugger::unique($form, $form->name);
            }
        });
    }

    /**
     * @return HasMany<FormSubmission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * The schema as value objects.
     *
     * @return array<int, FormField>
     */
    public function fieldDefinitions(): array
    {
        return array_map(FormField::fromArray(...), $this->fields ?? []);
    }

    protected static function newFactory(): FormFactory
    {
        return FormFactory::new();
    }
}

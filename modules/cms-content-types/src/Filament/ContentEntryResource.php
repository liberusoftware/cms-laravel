<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentTypes\Fields\FieldDefinition;
use Liberu\Cms\ContentTypes\Filament\Pages\ListContentEntries;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use RuntimeException;
use UnitEnum;

/**
 * Admin surface for content entries: items belonging to a custom content type.
 * The "Fields" section is built dynamically from the selected type's JSON field
 * schema, so each entry is edited with the fields its type declares. Owned by
 * the module.
 */
final class ContentEntryResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = ContentEntry::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-content-entries';

    #[\Override]
    protected static ?string $navigationLabel = 'Content Entries';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'title';

    protected static function cmsPermissionKey(): string
    {
        return 'content-entries';
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entry')
                ->columns(2)
                ->schema([
                    Select::make('content_type_id')
                        ->relationship('type', 'name')
                        ->required()
                        ->live()
                        ->preload()
                        ->searchable()
                        ->label('Content type'),
                    Select::make('status')
                        ->options(WorkflowState::options())
                        ->default(WorkflowState::Draft->value)
                        ->required(),
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('slug')
                        ->maxLength(255)
                        ->helperText('Leave blank to generate from the title.'),
                ]),
            Section::make('Fields')
                ->columns(1)
                ->schema(fn (Get $get): array => self::dataFieldsFor($get('content_type_id')))
                ->visible(fn (Get $get): bool => filled($get('content_type_id'))),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type.name')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Build the form fields for an entry's `data`, driven by the selected content
     * type's field schema.
     *
     * @return array<int, Field>
     */
    private static function dataFieldsFor(mixed $typeId): array
    {
        if (! is_numeric($typeId)) {
            return [];
        }

        $type = ContentType::query()->find((int) $typeId);

        if (! $type instanceof ContentType) {
            return [];
        }

        return array_map(self::componentFor(...), $type->fieldDefinitions());
    }

    private static function componentFor(FieldDefinition $field): Field
    {
        $name = "data.{$field->name}";
        $definition = app(FieldTypeRegistryInterface::class)->get($field->type);

        $component = $definition === null
            ? TextInput::make($name)
            : ($definition->component)($name, $field->options);

        if (! $component instanceof Field) {
            throw new RuntimeException("Field type [{$field->type}] did not produce a form field.");
        }

        return $component
            ->label($field->label)
            ->required($field->required);
    }

    /**
     * @return array<string, PageRegistration>
     */
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListContentEntries::route('/'),
        ];
    }
}

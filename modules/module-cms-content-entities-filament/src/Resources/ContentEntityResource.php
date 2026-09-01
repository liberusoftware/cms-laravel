<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesFilament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentEntitiesFilament\Resources\Pages\ListContentEntities;
use Liberu\Cms\ContentTypes\Actions\ContentEntryMutationService;
use Liberu\Cms\ContentTypes\Fields\FieldDefinition;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use RuntimeException;
use UnitEnum;

final class ContentEntityResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = ContentEntry::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-content-entities';

    #[\Override]
    protected static ?string $navigationLabel = 'Content Entities';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'title';

    protected static function cmsPermissionKey(): string
    {
        return 'content-entities';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Entity')->columns(2)->schema([
                Select::make('content_type_id')->relationship('type', 'name')->required()->live()->preload()->searchable(),
                Select::make('status')->options(WorkflowState::options())->default(WorkflowState::Draft->value)->required(),
                TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('slug')->maxLength(255),
            ]),
            Section::make('Fields')
                ->schema(fn (Get $get): array => self::fieldsFor($get('content_type_id')))
                ->visible(fn (Get $get): bool => filled($get('content_type_id'))),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('type.name')->label('Bundle')->badge()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('updated_at', 'desc')->recordActions([
            EditAction::make(),
            Action::make('clone')->icon(Heroicon::OutlinedSquare2Stack)->requiresConfirmation()->action(
                fn (ContentEntry $record): ContentEntry => app(ContentEntryMutationService::class)->clone($record),
            )->successNotificationTitle('Content entity cloned'),
            DeleteAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContentEntities::route('/')];
    }

    /** @return array<int, Field> */
    private static function fieldsFor(mixed $typeId): array
    {
        if (! is_numeric($typeId)) {
            return [];
        }

        $type = ContentType::query()->find((int) $typeId);
        if (! $type instanceof ContentType) {
            return [];
        }

        return array_map(function (FieldDefinition $field): Field {
            $definition = app(FieldTypeRegistryInterface::class)->get($field->type);
            $component = $definition === null
                ? TextInput::make('data.'.$field->name)
                : ($definition->component)('data.'.$field->name, $field->options);

            if (! $component instanceof Field) {
                throw new RuntimeException("Field type [{$field->type}] did not produce a form field.");
            }

            return $component->label($field->label)->required($field->required);
        }, $type->fieldDefinitions());
    }
}

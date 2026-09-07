<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemFilament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\FieldSystem\Models\FieldSchema;
use Liberu\Cms\FieldSystemFilament\Resources\Pages\CreateFieldSchema;
use Liberu\Cms\FieldSystemFilament\Resources\Pages\EditFieldSchema;
use Liberu\Cms\FieldSystemFilament\Resources\Pages\ListFieldSchemas;
use UnitEnum;

final class FieldSchemaResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = FieldSchema::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-field-schemas';

    #[\Override]
    protected static ?string $navigationLabel = 'Field Schemas';

    protected static function cmsPermissionKey(): string
    {
        return 'field-system';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Schema')->columns(2)->schema([
                TextInput::make('key')->required()->maxLength(255)->regex('/^[a-z0-9][a-z0-9_-]{0,254}$/'),
                TextInput::make('name')->required()->maxLength(255),
            ]),
            Section::make('Fields')->schema([
                Repeater::make('fields')->schema([
                    TextInput::make('name')->required()->maxLength(120)->regex('/^[a-z][a-z0-9_]{0,119}$/'),
                    TextInput::make('label')->maxLength(255),
                    Select::make('type')->options(app(FieldTypeRegistryInterface::class)->options())->required(),
                    Select::make('cardinality')->options(['one' => 'Single value', 'many' => 'Multiple values'])->default('one'),
                    Toggle::make('required'),
                    Toggle::make('computed'),
                    TextInput::make('default'),
                    TextInput::make('condition.field'),
                    TextInput::make('condition.equals'),
                    TextInput::make('validation.min')->numeric(),
                    TextInput::make('validation.max')->numeric(),
                    TagsInput::make('options'),
                    Fieldset::make('Collection limits')->schema([
                        TextInput::make('validation.minItems')->numeric(),
                        TextInput::make('validation.maxItems')->numeric(),
                    ])->columns(2)->columnSpanFull(),
                ])->columns(2)->reorderable()->collapsible()->default([]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('key')->badge()->searchable(),
            TextColumn::make('version')->sortable(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('name')->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => ListFieldSchemas::route('/'),
            'create' => CreateFieldSchema::route('/create'),
            'edit' => EditFieldSchema::route('/{record}/edit'),
        ];
    }

    public static function currentTeamId(): ?int
    {
        $team = auth()->user()?->currentTeam;
        $key = $team?->getKey();

        return is_int($key) ? $key : (is_string($key) && ctype_digit($key) ? (int) $key : null);
    }
}

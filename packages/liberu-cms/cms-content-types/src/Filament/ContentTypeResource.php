<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Filament;

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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentTypes\Fields\FieldType;
use Liberu\Cms\ContentTypes\Filament\Pages\ListContentTypes;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

/**
 * Admin surface for the Content Types module: user-defined content types whose
 * shape is a JSON field schema edited here as a repeater. Owned by the module.
 */
final class ContentTypeResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = ContentType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $slug = 'cms-content-types';

    protected static ?string $navigationLabel = 'Content Types';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    protected static function cmsPermissionKey(): string
    {
        return 'content-types';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Type')
                ->columns(2)
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Stable machine key, e.g. "portfolio_item".'),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('singular_label')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('plural_label')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make('Fields')
                ->schema([
                    Repeater::make('fields')
                        ->label('Field schema')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('label')
                                ->required()
                                ->maxLength(255),
                            Select::make('type')
                                ->options(FieldType::options())
                                ->default(FieldType::Text->value)
                                ->required(),
                            Toggle::make('required')
                                ->default(false),
                            TagsInput::make('options')
                                ->helperText('Choices for a Select field.')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add field')
                        ->reorderable()
                        ->collapsible()
                        ->default([]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->badge()
                    ->searchable(),
                TextColumn::make('entries_count')
                    ->label('Entries')
                    ->counts('entries')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
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
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListContentTypes::route('/'),
        ];
    }
}

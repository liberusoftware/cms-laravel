<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Forms\Fields\FormFieldType;
use Liberu\Cms\Forms\Filament\Pages\ListForms;
use Liberu\Cms\Forms\Models\Form;
use UnitEnum;

/**
 * Admin surface for the Forms module: name, slug, and a repeatable field schema.
 * Owned by the module so the dependency direction stays host → module.
 */
final class FormResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = Form::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-forms';

    #[\Override]
    protected static ?string $navigationLabel = 'Forms';

    #[\Override]
    protected static ?string $recordTitleAttribute = 'name';

    protected static function cmsPermissionKey(): string
    {
        return 'forms';
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->maxLength(255)
                        ->helperText('Leave blank to generate from the name.'),
                    Repeater::make('fields')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('label')
                                ->required()
                                ->maxLength(255),
                            Select::make('type')
                                ->options(FormFieldType::options())
                                ->default(FormFieldType::Text->value)
                                ->required(),
                            Toggle::make('required')
                                ->default(false),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->addActionLabel('Add field')
                        ->default([]),
                ]),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('submissions_count')
                    ->counts('submissions')
                    ->label('Submissions'),
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
     * @return array<string, PageRegistration>
     */
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
        ];
    }
}

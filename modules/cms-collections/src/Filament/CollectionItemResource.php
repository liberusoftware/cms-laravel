<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Filament;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Collections\Filament\Pages\ListCollectionItems;
use Liberu\Cms\Collections\Models\CollectionItem;
use Liberu\Cms\Contracts\Content\WorkflowState;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

final class CollectionItemResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = CollectionItem::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static function cmsPermissionKey(): string
    {
        return 'collections';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('collection_id')->relationship('collection', 'name')->required()->searchable()->preload(),
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('slug')->maxLength(255)->helperText('Leave blank to generate from the title.'),
            Select::make('status')->options(WorkflowState::options())->default(WorkflowState::Draft->value)->required(),
            Textarea::make('content')->rows(12)->columnSpanFull(),
            Textarea::make('data')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                ->json()
                ->helperText('Structured record data.')
                ->columnSpanFull(),
            Textarea::make('metadata')
                ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
                ->json()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('collection.name')->label('Collection')->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('published_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListCollectionItems::route('/')];
    }
}

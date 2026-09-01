<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesFilament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContentEntitiesFilament\Resources\Pages\ListContentBundles;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

final class ContentBundleResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = ContentType::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-content-bundles';

    protected static function cmsPermissionKey(): string
    {
        return 'content-entities';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Bundle')->columns(2)->schema([
                TextInput::make('key')->required()->maxLength(255),
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('singular_label')->required()->maxLength(255),
                TextInput::make('plural_label')->required()->maxLength(255),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('key')->badge()->searchable(),
            TextColumn::make('entries_count')->counts('entries')->sortable(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContentBundles::route('/')];
    }
}

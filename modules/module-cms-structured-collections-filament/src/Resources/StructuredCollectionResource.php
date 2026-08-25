<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsFilament\Resources;

use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

final class StructuredCollectionResource extends Resource
{
    use AuthorizesWithPermissions;
    protected static ?string $model = Collection::class;
    protected static ?string $slug = 'cms-structured-collections';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;
    protected static string|UnitEnum|null $navigationGroup = 'CMS';
    protected static function cmsPermissionKey(): string { return 'collections'; }
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('name')->required()->maxLength(255), TextInput::make('slug')->maxLength(255), TextInput::make('type')->required()->maxLength(64), Textarea::make('description'), Textarea::make('schema')->json()]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->searchable()->sortable(), TextColumn::make('slug')->searchable(), TextColumn::make('type'), TextColumn::make('items_count')->counts('items')]); }
    public static function getPages(): array { return ['index' => Pages\ListStructuredCollections::route('/')]; }
}

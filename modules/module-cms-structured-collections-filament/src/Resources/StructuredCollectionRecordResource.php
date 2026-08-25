<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsFilament\Resources;

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
use Liberu\Cms\Collections\Models\CollectionItem;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use UnitEnum;

final class StructuredCollectionRecordResource extends Resource
{
    use AuthorizesWithPermissions;
    protected static ?string $model = CollectionItem::class;
    protected static ?string $slug = 'cms-structured-collection-records';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'CMS';
    protected static function cmsPermissionKey(): string { return 'collections'; }
    public static function form(Schema $schema): Schema { return $schema->components([Select::make('collection_id')->relationship('collection', 'name')->required()->searchable(), TextInput::make('title')->required()->maxLength(255), TextInput::make('slug')->maxLength(255), Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->required(), Textarea::make('content'), Textarea::make('data')->json(), Textarea::make('metadata')->json()]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('title')->searchable()->sortable(), TextColumn::make('collection.name'), TextColumn::make('status')->badge(), TextColumn::make('published_at')->dateTime()]); }
    public static function getPages(): array { return ['index' => Pages\ListStructuredCollectionRecords::route('/')]; }
}

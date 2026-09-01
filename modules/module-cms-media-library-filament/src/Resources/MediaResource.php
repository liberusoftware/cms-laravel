<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryFilament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Media\Models\Media;
use Liberu\Cms\MediaLibraryFilament\Resources\Pages\ListMedia;
use UnitEnum;

final class MediaResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $slug = 'cms-media';

    protected static ?string $navigationLabel = 'Media';

    protected static ?string $recordTitleAttribute = 'file_name';

    protected static function cmsPermissionKey(): string
    {
        return 'media';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('file_name')->required()->maxLength(255),
            TextInput::make('folder')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('preview')->label('')->getStateUsing(fn (Media $record): string => $record->url()),
            TextColumn::make('file_name')->searchable()->sortable(),
            TextColumn::make('mime_type')->badge()->sortable(),
            TextColumn::make('size')->formatStateUsing(fn (int $state): string => number_format($state / 1024, 1).' KB')->sortable(),
            TextColumn::make('folder')->badge()->placeholder('root')->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->defaultSort('created_at', 'desc')
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListMedia::route('/')];
    }
}

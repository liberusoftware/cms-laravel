<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioFilament\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\VideoAndAudio\Models\MediaAsset;
use UnitEnum;

final class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFilm;
    protected static string|UnitEnum|null $navigationGroup = 'CMS';
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('title')->required()->maxLength(255), Select::make('kind')->options(['video' => 'Video', 'audio' => 'Audio'])->required(), Select::make('source_type')->options(['upload' => 'Upload', 'remote' => 'Remote'])->required(), TextInput::make('source_uri')->required(), TextInput::make('mime_type'), TextInput::make('duration_seconds')->numeric()]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('title')->searchable()->sortable(), TextColumn::make('kind')->badge(), TextColumn::make('source_type')->badge(), TextColumn::make('status')->badge(), TextColumn::make('duration_seconds')])->defaultSort('created_at', 'desc'); }
    public static function getPages(): array { return ['index' => Pages\ListMediaAssets::route('/')]; }
}

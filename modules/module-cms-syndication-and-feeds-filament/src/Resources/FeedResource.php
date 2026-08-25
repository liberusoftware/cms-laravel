<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\SyndicationAndFeeds\Models\Feed;

final class FeedResource extends Resource
{
    #[\Override]
    protected static ?string $model = Feed::class;

    #[\Override]
    protected static ?string $slug = 'cms-syndication-and-feeds';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('key')->required()->alphaDash(), TextInput::make('title')->required(), Select::make('format')->options(['rss' => 'RSS', 'atom' => 'Atom', 'json' => 'JSON Feed'])->required(), TextInput::make('source_url')->url()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('key'), TextColumn::make('title'), TextColumn::make('format')->badge(), TextColumn::make('active')->badge(), TextColumn::make('items_count')->counts('items')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListFeeds::route('/')];
    }
}

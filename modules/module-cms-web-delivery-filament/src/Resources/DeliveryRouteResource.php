<?php

declare(strict_types=1);

namespace Liberu\Cms\WebDeliveryFilament\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\WebDelivery\Actions\WebDeliveryService;
use Liberu\Cms\WebDelivery\Models\DeliveryRoute;
use UnitEnum;

final class DeliveryRouteResource extends Resource
{
    protected static ?string $model = DeliveryRoute::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('path')->required()->maxLength(2048),
            Select::make('route_type')->options(['content' => 'Content', 'redirect' => 'Redirect', 'error' => 'Error'])->required(),
            TextInput::make('canonical_url')->url(),
            TextInput::make('redirect_url')->url(),
            TextInput::make('redirect_status')->numeric(),
            Textarea::make('body'),
            Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('path')->searchable()->sortable(),
            TextColumn::make('route_type')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('maintenance')->boolean(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([
            \Filament\Actions\Action::make('maintenance')->label('Toggle maintenance')->action(fn (DeliveryRoute $record): DeliveryRoute => app(WebDeliveryService::class)->setMaintenance($record, ! $record->maintenance)),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListDeliveryRoutes::route('/')];
    }
}

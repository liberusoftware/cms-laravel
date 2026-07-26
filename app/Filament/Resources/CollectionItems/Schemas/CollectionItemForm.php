<?php

namespace App\Filament\Resources\CollectionItems\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollectionItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('collection_id')
                            ->required()
                            ->relationship('collection', 'name')
                            ->columnSpanFull(),
                        TextInput::make('title')
                            ->required(),
                        TextInput::make('slug')
                            ->required(),
                        Textarea::make('content')
                            ->columnSpanFull(),
                        TextInput::make('status')
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('published_at'),
                    ]),
            ]);
    }
}

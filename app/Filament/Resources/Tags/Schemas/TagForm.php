<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TagForm
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
                            ->relationship('collection', 'name')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->required(),
                    ]),
            ]);
    }
}

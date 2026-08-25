<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryFilament\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\ContactDirectory\Models\Contact;

final class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationLabel = 'Contacts';

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('department'), TextColumn::make('email'), TextColumn::make('is_public')->boolean()]);
    }
}

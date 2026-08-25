<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Redirects\Models\Redirect;

final class RedirectResource extends Resource
{
    #[\Override]
    protected static ?string $model = Redirect::class;

    #[\Override]
    protected static ?string $slug = 'cms-redirects';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('from_path')->required(), TextInput::make('to_path')->required(), Select::make('status_code')->options([301 => '301 Permanent', 302 => '302 Temporary', 307 => '307 Temporary', 308 => '308 Permanent'])->default(301)->required(), TextInput::make('source')->default('manual')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('from_path')->searchable(), TextColumn::make('to_path')->searchable(), TextColumn::make('status_code')->badge(), TextColumn::make('hit_count')->sortable(), TextColumn::make('source')]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => Pages\ListRedirects::route('/')];
    }
}

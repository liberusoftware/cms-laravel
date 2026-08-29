<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class CopilotRequestResource extends Resource
{
    protected static ?string $slug = 'cms-copilot-requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('capability')->options(['search' => 'Search', 'summary' => 'Summary', 'draft' => 'Draft', 'transform' => 'Transform', 'metadata' => 'Metadata', 'internal-links' => 'Internal links', 'action-confirmation' => 'Action confirmation'])->required(), Textarea::make('prompt')->required()->maxLength(10000)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('capability')->badge(), TextColumn::make('status')->badge(), TextColumn::make('created_at')->dateTime()]);
    }
}

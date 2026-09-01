<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class BackupArtifactResource extends Resource
{
    #[\Override]
    protected static ?string $slug = 'backup-artifacts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), Select::make('artifact_type')->options(['full' => 'Full', 'content' => 'Content', 'configuration' => 'Configuration', 'database' => 'Database', 'files' => 'Files'])->required(), TextInput::make('path')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('artifact_type')->badge(), TextColumn::make('status')->badge(), TextColumn::make('expires_at')->dateTime()]);
    }
}

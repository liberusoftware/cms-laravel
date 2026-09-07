<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantFilament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class AccessibilityResource extends Resource
{
    #[\Override]
    protected static ?string $slug = 'accessibility-assistant';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Textarea::make('html')->label('Content to analyze')->rows(12)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table;
    }
}

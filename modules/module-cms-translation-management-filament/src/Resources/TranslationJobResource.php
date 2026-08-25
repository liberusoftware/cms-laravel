<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementFilament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\TranslationManagement\Actions\TranslationManagementService;
use Liberu\Cms\TranslationManagement\Models\TranslationJob;
use Liberu\Cms\TranslationManagementFilament\Resources\Pages\ListTranslationJobs;
use UnitEnum;

final class TranslationJobResource extends Resource
{
    #[\Override]
    protected static ?string $model = TranslationJob::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('source_locale')->required()->maxLength(16),
            TextInput::make('target_locale')->required()->maxLength(16),
            TextInput::make('vendor_key')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('source_locale'),
            TextColumn::make('target_locale'),
            TextColumn::make('status')->badge(),
            TextColumn::make('completed_units')->formatStateUsing(fn ($state, TranslationJob $record): string => "{$state}/{$record->total_units}"),
            TextColumn::make('actual_cost')->money('USD'),
        ])->defaultSort('created_at', 'desc')->recordActions([
            Action::make('reconcile')->icon(Heroicon::OutlinedArrowPath)->action(fn (TranslationJob $record): TranslationJob => app(TranslationManagementService::class)->reconcile($record)),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListTranslationJobs::route('/')];
    }
}

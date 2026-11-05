<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Cms\Contracts\Content\WorkflowState;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading(__('Create your first page'))
            ->emptyStateDescription(__('Pages are the building blocks of your website. Start with a home page or an introduction.'))
            ->emptyStateIcon('heroicon-o-document-text')
            ->columns([
                TextColumn::make('title')
                    ->weight('medium')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (WorkflowState|string $state): string => match ($state instanceof WorkflowState ? $state->value : $state) {
                        'published' => 'success',
                        'review' => 'warning',
                        'archived' => 'gray',
                        default => 'info',
                    })
                    ->searchable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->placeholder(__('Not published'))
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label(__('Author ID'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('team_id')
                    ->label(__('Team ID'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft' => __('Draft'), 'review' => __('In review'),
                    'published' => __('Published'), 'archived' => __('Archived'),
                ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

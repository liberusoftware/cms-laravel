<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Cms\Core\Filament\Concerns\AuthorizesWithPermissions;
use Liberu\Cms\Forms\Filament\Pages\ListFormSubmissions;
use Liberu\Cms\Forms\Models\FormSubmission;
use UnitEnum;

/**
 * Read-only admin surface for viewing (and pruning) form submissions.
 */
final class FormSubmissionResource extends Resource
{
    use AuthorizesWithPermissions;

    #[\Override]
    protected static ?string $model = FormSubmission::class;

    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    #[\Override]
    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    #[\Override]
    protected static ?string $slug = 'cms-form-submissions';

    #[\Override]
    protected static ?string $navigationLabel = 'Form submissions';

    protected static function cmsPermissionKey(): string
    {
        return 'form-submissions';
    }

    #[\Override]
    public static function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.name')
                    ->label('Form')
                    ->sortable(),
                TextColumn::make('data')
                    ->label('Submission')
                    ->formatStateUsing(fn (mixed $state): string => is_array($state) ? (json_encode($state, JSON_UNESCAPED_SLASHES) ?: '') : '')
                    ->wrap()
                    ->limit(120),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<string, PageRegistration>
     */
    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListFormSubmissions::route('/'),
        ];
    }
}

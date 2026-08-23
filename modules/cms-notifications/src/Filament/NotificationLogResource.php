<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Filament;

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
use Liberu\Cms\Notifications\Filament\Pages\ListNotificationLogs;
use Liberu\Cms\Notifications\Models\NotificationLog;
use UnitEnum;

/**
 * Read-only admin surface for the notification audit log. The log is a global
 * system record, so it is not scoped to a tenant.
 */
final class NotificationLogResource extends Resource
{
    use AuthorizesWithPermissions;

    protected static ?string $model = NotificationLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $slug = 'cms-notifications';

    protected static ?string $navigationLabel = 'Notifications';

    protected static bool $isScopedToTenant = false;

    protected static function cmsPermissionKey(): string
    {
        return 'notification-logs';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')
                    ->badge()
                    ->sortable(),
                TextColumn::make('channel')
                    ->sortable(),
                TextColumn::make('recipient')
                    ->toggleable(),
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
    public static function getPages(): array
    {
        return [
            'index' => ListNotificationLogs::route('/'),
        ];
    }
}

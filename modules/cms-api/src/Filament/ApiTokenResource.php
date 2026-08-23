<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Filament;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;
use Liberu\Cms\Api\Filament\Pages\ListApiTokens;
use Liberu\Cms\Contracts\Access\AccessControlInterface;
use UnitEnum;

/**
 * Manages Delivery API tokens for the current Team from the panel: list, mint
 * (via the list page's header action, which reveals the plaintext once), and
 * revoke. Tokens are the Team's Sanctum personal access tokens; the query is
 * scoped to the panel tenant so one Team never sees another's tokens. Gated by
 * the module-owned `api-tokens.manage` permission.
 */
final class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string|UnitEnum|null $navigationGroup = 'CMS';

    protected static ?string $slug = 'cms-api-tokens';

    protected static ?string $navigationLabel = 'API tokens';

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isScopedToTenant = false;

    public static function canViewAny(): bool
    {
        return app()->bound(AccessControlInterface::class)
            && app(AccessControlInterface::class)->can('api-tokens.manage');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Model) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('tokenable_type', $tenant->getMorphClass())
            ->where('tokenable_id', $tenant->getKey());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('abilities')
                    ->badge(),
                TextColumn::make('last_used_at')
                    ->label('Last used')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                DeleteAction::make()
                    ->label('Revoke'),
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
            'index' => ListApiTokens::route('/'),
        ];
    }
}

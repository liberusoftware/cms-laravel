<?php

declare(strict_types=1);

namespace Liberu\Cms\Api\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Liberu\Cms\Api\Filament\ApiTokenResource;

final class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create token')
                ->icon('heroicon-o-plus')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->default('delivery'),
                    Toggle::make('write')
                        ->label('Grant write access (content:write)')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    $tenant = Filament::getTenant();

                    if (! $tenant instanceof HasApiTokens) {
                        Notification::make()
                            ->title('No team is available to issue a token for.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $name = is_string($data['name'] ?? null) && $data['name'] !== '' ? $data['name'] : 'delivery';

                    $abilities = ['content:read'];

                    if (($data['write'] ?? false) === true) {
                        $abilities[] = 'content:write';
                    }

                    $token = $tenant->createToken($name, $abilities);

                    Notification::make()
                        ->title('Delivery token created')
                        ->body('Copy it now — it will not be shown again: '.$token->plainTextToken)
                        ->persistent()
                        ->success()
                        ->send();
                }),
        ];
    }
}

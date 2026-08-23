<?php

declare(strict_types=1);

namespace Liberu\Cms\Media\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\UploadedFile;
use Liberu\Cms\Media\Exceptions\InvalidUpload;
use Liberu\Cms\Media\Filament\MediaResource;
use Liberu\Cms\Media\Media\StoreUpload;

final class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upload')
                ->label('Upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
                    FileUpload::make('file')
                        ->required()
                        ->storeFiles(false),
                    TextInput::make('folder')
                        ->maxLength(255)
                        ->helperText('Optional sub-folder.'),
                ])
                ->action(function (array $data): void {
                    $file = $data['file'] ?? null;

                    if (! $file instanceof UploadedFile) {
                        return;
                    }

                    $rawFolder = $data['folder'] ?? null;
                    $folder = is_string($rawFolder) && $rawFolder !== '' ? $rawFolder : null;

                    try {
                        app(StoreUpload::class)($file, $folder);
                    } catch (InvalidUpload $exception) {
                        Notification::make()
                            ->title('Upload rejected.')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('File uploaded.')
                        ->success()
                        ->send();
                }),
        ];
    }
}

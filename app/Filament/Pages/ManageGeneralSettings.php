<?php

namespace App\Filament\Pages;

use App\Filament\Admin\Pages\PlatformSetup;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends SettingsPage
{
    #[\Override]
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    #[\Override]
    protected static string $settings = GeneralSettings::class;

    #[\Override]
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    #[\Override]
    protected static ?string $title = 'General Settings';

    #[\Override]
    protected static ?string $navigationLabel = 'General Settings';

    public static function canAccess(): bool
    {
        return PlatformSetup::canAccess();
    }

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Site Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('site_name')
                            ->required(),
                        Toggle::make('site_active')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false)
                            ->required(),
                        TextInput::make('site_email')
                            ->email()
                            ->required(),
                        TextInput::make('site_phone')
                            ->tel(),
                        TextInput::make('site_address'),
                        TextInput::make('site_country'),
                        TextInput::make('site_currency')
                            ->required(),
                        TextInput::make('site_default_language')
                            ->required(),
                    ]),
                Section::make('Social Media Links')
                    ->description('Add your social media profile URLs')
                    ->columns(2)
                    ->schema([
                        TextInput::make('facebook_url'),
                        TextInput::make('twitter_url'),
                        TextInput::make('github_url'),
                        TextInput::make('youtube_url'),

                    ]),
                Section::make('Footer')
                    ->schema([
                        Textarea::make('footer_copyright')
                            ->label('Copyright Text')
                            ->required()
                            ->maxLength(500)
                            ->rows(2),
                    ]),
            ]);
    }
}

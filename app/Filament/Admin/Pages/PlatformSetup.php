<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\WebsiteSetup;
use App\Settings\GeneralSettings;
use App\Support\Onboarding\SetupManager;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

/** @property-read Schema $form */
class PlatformSetup extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Platform setup';

    protected static ?string $title = 'Set up your CMS platform';

    protected static ?int $navigationSort = -1;

    protected string $view = 'filament.pages.setup';

    /** @var array<string, string|null>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'admin' && (bool) auth()->user()?->is_platform_admin;
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
        $progress = app(SetupManager::class)->progress(auth()->user(), null);
        $settings = app(GeneralSettings::class);
        $this->form->fill($progress?->data ?: [
            'site_name' => $settings->site_name,
            'site_email' => $settings->site_email,
            'site_default_language' => $settings->site_default_language,
            'site_currency' => in_array($settings->site_currency, ['USD', 'GBP', 'EUR', 'CAD', 'AUD', 'NZD', 'JPY', 'CHF'], true) ? $settings->site_currency : 'USD',
        ]);
    }

    public function getSubheading(): ?string
    {
        return __('Set shared platform defaults. Each website team has its own separate website setup.');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Wizard::make([
                Step::make(__('Platform identity'))->description(__('Name & contact'))->icon(Heroicon::OutlinedBuildingOffice)
                    ->schema([
                        TextInput::make('site_name')->label(__('Platform name'))->required()->maxLength(255)->autofocus(),
                        TextInput::make('site_email')->label(__('Contact email'))->email()->required()->maxLength(255)
                            ->helperText(__('The public contact address in your general settings. This does not configure email delivery.')),
                    ])->afterValidation(fn () => $this->saveProgress()),
                Step::make(__('Regional defaults'))->description(__('Language & currency'))->icon(Heroicon::OutlinedLanguage)
                    ->schema([
                        Select::make('site_default_language')->label(__('Default language'))->options(WebsiteSetup::languages())->searchable()->required(),
                        Select::make('site_currency')->label(__('Default currency'))->options(array_combine(['USD', 'GBP', 'EUR', 'CAD', 'AUD', 'NZD', 'JPY', 'CHF'], ['USD', 'GBP', 'EUR', 'CAD', 'AUD', 'NZD', 'JPY', 'CHF']))->required(),
                    ])->afterValidation(fn () => $this->saveProgress()),
                Step::make(__('Review & finish'))->description(__('Start administering'))->icon(Heroicon::OutlinedCheckCircle)
                    ->schema([
                        Text::make(fn () => __('Platform: :name · Contact: :email', ['name' => $this->data['site_name'] ?? '', 'email' => $this->data['site_email'] ?? ''])),
                        Text::make(fn () => __('Language: :language · Currency: :currency', ['language' => $this->data['site_default_language'] ?? '', 'currency' => $this->data['site_currency'] ?? ''])),
                        Text::make(__('Save these defaults to finish platform setup. You can update them at any time in General Settings.')),
                        Text::make(__('Before inviting your teams, verify email delivery, backups and two-factor authentication with your deployment administrator. Website setup does not publish content or enable modules.')),
                    ]),
            ])->submitAction(new HtmlString(view('filament.pages.setup-submit')->render())),
        ]);
    }

    public function saveProgress(): void
    {
        abort_unless(static::canAccess(), 403);
        app(SetupManager::class)->save(auth()->user(), null, $this->data ?? []);
    }

    public function complete(): void
    {
        abort_unless(static::canAccess(), 403);
        app(SetupManager::class)->save(auth()->user(), null, $this->form->getState(), true);
        Notification::make()->title(__('Platform setup complete'))->success()->send();
        $this->redirect(PlatformDashboard::getUrl());
    }
}

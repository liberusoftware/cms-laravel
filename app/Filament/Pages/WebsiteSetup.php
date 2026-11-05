<?php

namespace App\Filament\Pages;

use App\Models\Team;
use App\Models\User;
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
use Livewire\Attributes\Locked;

/** @property-read Schema $form */
class WebsiteSetup extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Website setup';

    protected static ?string $title = 'Set up your website';

    protected static ?int $navigationSort = -1;

    protected string $view = 'filament.pages.setup';

    /** @var array<string, string|null>|null */
    public ?array $data = [];

    #[Locked]
    public ?int $setupTeamId = null;

    public static function canAccess(): bool
    {
        $team = Filament::getTenant();

        $user = auth()->user();

        return Filament::getCurrentPanel()?->getId() === 'app' && $team instanceof Team
            && $user instanceof User && $user->ownsTeam($team) && $user->canAccessTenant($team);
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
        $team = $this->setupTeam();
        $this->setupTeamId = $team->id;
        $progress = app(SetupManager::class)->progress(auth()->user(), $team);
        $this->form->fill($progress?->data ?: ['name' => $team->name, 'default_locale' => 'en', 'timezone' => 'UTC']);
    }

    public function getSubheading(): ?string
    {
        return __('A few essentials, then you can start creating. Your website stays in draft until you are ready.');
    }

    /** @return array<string, string> */
    public static function languages(): array
    {
        return ['en' => 'English', 'fr' => 'Français', 'de' => 'Deutsch', 'es' => 'Español', 'it' => 'Italiano', 'pt' => 'Português', 'nl' => 'Nederlands'];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Wizard::make([
                Step::make(__('Website details'))->description(__('Make it yours'))->icon(Heroicon::OutlinedGlobeAlt)
                    ->schema([
                        TextInput::make('name')->label(__('Website name'))->required()->maxLength(255)->autofocus(),
                        TextInput::make('domain')->label(__('Website domain'))->placeholder('example.com')->maxLength(253)
                            ->helperText(__('Optional. Enter a domain without https:// or a path. You can connect it later.')),
                    ])->afterValidation(fn () => $this->saveProgress()),
                Step::make(__('Language & time'))->description(__('Local defaults'))->icon(Heroicon::OutlinedLanguage)
                    ->schema([
                        Select::make('default_locale')->label(__('Default language'))->options(static::languages())->required()->searchable(),
                        Select::make('timezone')->label(__('Timezone'))->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))->searchable()->required()
                            ->helperText(__('Choose the timezone your publishing team works in.')),
                    ])->afterValidation(fn () => $this->saveProgress()),
                Step::make(__('Ready to create'))->description(__('Your next steps'))->icon(Heroicon::OutlinedCheckCircle)
                    ->schema([
                        Text::make(fn () => __('Website: :name · Domain: :domain', ['name' => $this->data['name'] ?? '', 'domain' => $this->data['domain'] ?? __('Connect later')])),
                        Text::make(fn () => __('Language: :language · Timezone: :timezone', ['language' => $this->data['default_locale'] ?? '', 'timezone' => $this->data['timezone'] ?? ''])),
                        Text::make(__('Finish setup to save your website as a draft. Next, create your first page, add images, and review your content before publishing.')),
                        Text::make(__('Need more control? Your existing collections, modules and developer tools remain available according to your permissions.')),
                    ]),
            ])->submitAction(new HtmlString(view('filament.pages.setup-submit')->render())),
        ]);
    }

    public function saveProgress(): void
    {
        $this->assertContext();
        app(SetupManager::class)->save(auth()->user(), $this->setupTeam(), $this->data ?? []);
    }

    public function complete(): void
    {
        $this->assertContext();
        app(SetupManager::class)->save(auth()->user(), $this->setupTeam(), $this->form->getState(), true);
        Notification::make()->title(__('Your website is ready for content'))->success()->send();
        $this->redirect(Workspace::getUrl());
    }

    private function setupTeam(): Team
    {
        $team = Filament::getTenant();
        abort_unless($team instanceof Team, 403);

        return $team;
    }

    private function assertContext(): void
    {
        abort_unless(static::canAccess() && Filament::getTenant()?->getKey() === $this->setupTeamId, 403);
    }
}

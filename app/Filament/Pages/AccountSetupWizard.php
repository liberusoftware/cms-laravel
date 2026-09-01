<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Team;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @property-read Schema $form
 */
final class AccountSetupWizard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|\UnitEnum|null $navigationGroup = 'Account';

    protected static ?string $navigationLabel = 'Setup guide';

    protected static ?string $title = 'Finish setting up your workspace';

    protected static ?int $navigationSort = -100;

    protected string $view = 'filament.pages.account-setup-wizard';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $team = $user?->currentTeam;

        if (! $team instanceof Team) {
            $team = null;
        }

        $settings = is_array($team?->settings) ? $team->settings : [];
        $integrations = is_array($settings['integrations'] ?? null) ? $settings['integrations'] : [];
        $this->data = [
            'name' => is_string($user?->name) ? $user->name : '',
            'team_name' => is_string($team?->name) ? $team->name : '',
            'timezone' => is_string($settings['timezone'] ?? null) ? $settings['timezone'] : config('app.timezone', 'UTC'),
            'locale' => is_string($settings['locale'] ?? null) ? $settings['locale'] : config('app.locale', 'en'),
            'oauth_provider' => is_string($integrations['oauth_provider'] ?? null) ? $integrations['oauth_provider'] : 'none',
            'oauth_client_id' => '',
            'oauth_client_secret' => '',
            'delivery_api_key' => '',
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Account')
                        ->description('Confirm your profile details.')
                        ->schema([
                            TextInput::make('name')->label('Your name')->required()->maxLength(255),
                        ]),
                    Step::make('Workspace')
                        ->description('Name your current team and choose its defaults.')
                        ->schema([
                            TextInput::make('team_name')->label('Team name')->required()->maxLength(255),
                            Select::make('timezone')->options($this->timezones())->searchable()->required(),
                            Select::make('locale')->options(['en' => 'English'])->required(),
                        ]),
                    Step::make('Integrations')
                        ->description('Optional credentials are encrypted and scoped to this team.')
                        ->schema([
                            Section::make('OAuth')
                                ->description('Connect a provider only if your workflow needs social login or an external integration.')
                                ->schema([
                                    Select::make('oauth_provider')->options([
                                        'none' => 'Not now',
                                        'github' => 'GitHub',
                                        'google' => 'Google',
                                        'gitlab' => 'GitLab',
                                    ])->native(false)->required(),
                                    TextInput::make('oauth_client_id')->label('Client ID')->maxLength(255),
                                    TextInput::make('oauth_client_secret')->label('Client secret')->password()->revealable()->maxLength(255),
                                ])->columns(2),
                            Section::make('Delivery API')
                                ->description('Optional team delivery key for headless API consumers. Leave blank to create one later from API Tokens.')
                                ->schema([
                                    TextInput::make('delivery_api_key')->label('API key')->password()->revealable()->maxLength(4096),
                                ]),
                        ]),
                ])->submitAction(Action::make('complete')->label('Finish setup')->submit('complete')),
            ]);
    }

    public function complete(): void
    {
        $state = $this->form->getState();
        $user = auth()->user();
        $team = $user?->currentTeam;

        if ($user === null || ! $team instanceof Team) {
            throw ValidationException::withMessages(['setup' => 'Choose a workspace before completing setup.']);
        }

        $oauthProvider = $state['oauth_provider'] ?? 'none';
        $clientId = $state['oauth_client_id'] ?? '';
        $clientSecret = $state['oauth_client_secret'] ?? '';

        if ($oauthProvider !== 'none' && (! is_string($clientId) || trim($clientId) === '' || ! is_string($clientSecret) || trim($clientSecret) === '')) {
            throw ValidationException::withMessages(['oauth_client_id' => 'Provide both OAuth credentials or choose Not now.']);
        }

        DB::transaction(function () use ($state, $user, $team, $oauthProvider, $clientId, $clientSecret): void {
            $user->forceFill([
                'name' => $state['name'],
                'setup_completed_at' => now(),
            ])->save();

            $settings = is_array($team->settings) ? $team->settings : [];
            $settings['timezone'] = $state['timezone'];
            $settings['locale'] = $state['locale'];
            $settings['integrations'] = array_filter([
                'oauth_provider' => $oauthProvider,
                'oauth_client_id' => $clientId,
                'oauth_client_secret' => $clientSecret,
                'delivery_api_key' => $state['delivery_api_key'] ?? '',
            ], static fn (mixed $value): bool => is_string($value) && trim($value) !== '');

            $team->forceFill(['name' => $state['team_name'], 'settings' => $settings])->save();
        });

        session()->forget('account_setup_required');
        $this->redirect(Dashboard::getUrl());
    }

    protected function getViewData(): array
    {
        return ['heading' => 'Welcome to Liberu CMS', 'description' => 'A few guided choices will prepare your account and current workspace.'];
    }

    /** @return array<string, string> */
    private function timezones(): array
    {
        return collect(timezone_identifiers_list())->mapWithKeys(fn (string $timezone): array => [$timezone => $timezone])->all();
    }
}

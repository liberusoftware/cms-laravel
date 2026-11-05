<?php

namespace App\Filament\Pages;

use App\Filament\Admin\Pages\PlatformSetup;
use App\Filament\Resources\Collections\CollectionResource;
use App\Filament\Resources\Pages\PageResource;
use App\Models\Team;
use App\Support\Onboarding\SetupManager;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class Workspace extends Dashboard
{
    protected static ?string $slug = 'dashboard';

    protected static ?string $title = 'Website overview';

    protected static ?string $navigationLabel = 'Overview';

    public function getSubheading(): ?string
    {
        return __('Your content, your team, and a clear next step.');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            View::make('filament.pages.overview'),
            $this->getWidgetsContentComponent(),
        ]);
    }

    /** @return array{complete: bool, url: string, platform: bool}|null */
    public function setupDetails(): ?array
    {
        $platform = Filament::getCurrentPanel()?->getId() === 'admin';
        $team = Filament::getTenant();
        if ($platform ? ! PlatformSetup::canAccess() : ! WebsiteSetup::canAccess()) {
            return null;
        }
        $setup = app(SetupManager::class)->progress(auth()->user(), $platform || ! $team instanceof Team ? null : $team);

        return [
            'complete' => $setup?->completed_at !== null,
            'url' => $platform ? PlatformSetup::getUrl() : WebsiteSetup::getUrl(),
            'platform' => $platform,
        ];
    }

    /** @return list<array{title: string, description: string, icon: string, url: string}> */
    public function quickLinks(): array
    {
        $links = [];
        $panel = Filament::getCurrentPanel();
        if ($panel === null) {
            return [];
        }
        foreach ([PageResource::class => [__('Pages'), __('Create and organize the pages of your website.'), 'heroicon-o-document-text'], CollectionResource::class => [__('Collections'), __('Structure reusable content for your website.'), 'heroicon-o-squares-2x2']] as $resource => [$title, $description, $icon]) {
            if (in_array($resource, $panel->getResources(), true) && $resource::canViewAny()) {
                $links[] = ['title' => $title, 'description' => $description, 'icon' => $icon, 'url' => $resource::getUrl()];
            }
        }
        if ($panel->getId() === 'admin') {
            $links[] = ['title' => __('General settings'), 'description' => __('Manage shared identity, contact details and regional defaults.'), 'icon' => 'heroicon-o-cog-6-tooth', 'url' => ManageGeneralSettings::getUrl()];
            $links[] = ['title' => __('Website workspace'), 'description' => __('Return to your team to manage website content.'), 'icon' => 'heroicon-o-globe-alt', 'url' => url('/admin')];
        }

        return $links;
    }

    public function workspaceName(): string
    {
        $team = Filament::getTenant();

        return $team instanceof Team ? $team->name : __('CMS administration');
    }
}

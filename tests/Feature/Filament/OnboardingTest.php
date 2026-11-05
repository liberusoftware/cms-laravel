<?php

use App\Filament\Admin\Pages\PlatformDashboard;
use App\Filament\Admin\Pages\PlatformSetup;
use App\Filament\Pages\ManageGeneralSettings;
use App\Filament\Pages\WebsiteSetup;
use App\Filament\Pages\Workspace;
use App\Models\PanelSetup;
use App\Models\Team;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Support\Onboarding\SetupManager;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Core\Models\Site;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->owner = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->actingAs($this->owner);
    Filament::setCurrentPanel(Filament::getPanel('app'));
    Filament::setTenant($this->team);
    $this->website = ['name' => 'Acme studio', 'domain' => 'acme.example', 'default_locale' => 'en', 'timezone' => 'Europe/London'];
});

it('renders the overview and website wizard', function (): void {
    Livewire::test(Workspace::class)->assertSuccessful()->assertSee('Set up your website');
    Livewire::test(WebsiteSetup::class)->assertSuccessful()->assertSee('Website details');
});

it('resumes saved team progress without creating a site early', function (): void {
    Livewire::test(WebsiteSetup::class)->fillForm($this->website)->goToNextWizardStep()->assertHasNoErrors();
    expect(Site::count())->toBe(0);
    Livewire::test(WebsiteSetup::class)->assertFormSet($this->website);
});

it('creates a draft site for only the active team and completes idempotently', function (): void {
    $page = Livewire::test(WebsiteSetup::class)->fillForm($this->website)->call('complete')->assertHasNoErrors();
    $page->call('complete')->assertHasNoErrors();
    expect(Site::count())->toBe(1);
    $site = Site::firstOrFail();
    expect($site->name)->toBe('Acme studio')->and($site->team_id)->toBe($this->team->id)->and($site->status)->toBe('draft');
    expect(PanelSetup::firstOrFail()->completed_at)->not->toBeNull();
});

it('keeps progress and created sites separate for each owned website team', function (): void {
    app(SetupManager::class)->save($this->owner, $this->team, $this->website, true);
    auth()->logout();
    $second = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->actingAs($this->owner);
    Filament::setTenant($second);
    Livewire::test(WebsiteSetup::class)->assertFormSet(['name' => $second->name]);
    app(SetupManager::class)->save($this->owner, $second, [...$this->website, 'name' => 'Second website', 'domain' => 'second.example'], true);
    expect(Site::where('team_id', $second->id)->sole()->name)->toBe('Second website');
    expect(PanelSetup::count())->toBe(2);
});

it('rejects a tenant switch in an already mounted wizard', function (): void {
    $page = Livewire::test(WebsiteSetup::class)->fillForm($this->website);
    auth()->logout();
    $second = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->actingAs($this->owner);
    Filament::setTenant($second);
    $page->call('complete')->assertForbidden();
    expect(Site::count())->toBe(0);
});

it('denies setup to nonowners even when they are team members', function (): void {
    $member = User::factory()->create();
    $this->team->users()->attach($member);
    $this->actingAs($member);
    Livewire::test(WebsiteSetup::class)->assertForbidden();
    expect(fn () => app(SetupManager::class)->save($member, $this->team, $this->website, true))->toThrow(HttpException::class);
});

it('denies missing tenant and unauthenticated setup', function (): void {
    Filament::setTenant(null);
    Livewire::test(WebsiteSetup::class)->assertForbidden();
    auth()->logout();
    expect(fn () => app(SetupManager::class)->save(null, $this->team, $this->website, true))->toThrow(HttpException::class);
});

it('validates final setup and prevents premature completion', function (): void {
    Livewire::test(WebsiteSetup::class)->fillForm(['name' => '', 'timezone' => 'invalid'])->call('complete')->assertHasFormErrors(['name', 'timezone']);
    Livewire::test(WebsiteSetup::class)->fillForm([...$this->website, 'domain' => 'https://example.com/path'])->call('complete')->assertHasErrors(['data.domain']);
    expect(PanelSetup::count())->toBe(0)->and(Site::count())->toBe(0);
});

it('does not grant platform access to a team administrator', function (): void {
    expect($this->owner->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::setTenant(null);
    Livewire::test(PlatformSetup::class)->assertForbidden();
    Livewire::test(ManageGeneralSettings::class)->assertForbidden();
    $this->get('/platform-admin')->assertForbidden();
});

it('saves platform defaults independently of team setup', function (): void {
    $this->owner->forceFill(['is_platform_admin' => true])->save();
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::setTenant(null);
    Livewire::test(PlatformDashboard::class)->assertSuccessful()->assertSee('Set up your platform');
    Livewire::test(PlatformSetup::class)->fillForm([
        'site_name' => 'Acme CMS', 'site_email' => 'hello@acme.example',
        'site_default_language' => 'fr', 'site_currency' => 'EUR',
    ])->call('complete')->assertHasNoErrors();
    $settings = app(GeneralSettings::class);
    expect($settings->site_name)->toBe('Acme CMS')->and($settings->site_currency)->toBe('EUR');
    expect(PanelSetup::where('scope', 'platform')->sole()->completed_at)->not->toBeNull();
    expect(Site::count())->toBe(0);
});

it('grants and revokes platform access only through explicit provisioning', function (): void {
    $this->owner->fill(['is_platform_admin' => true])->save();
    expect($this->owner->fresh()->is_platform_admin)->toBeFalse();
    $this->artisan('cms:platform-admin', ['email' => $this->owner->email])->assertSuccessful();
    expect($this->owner->fresh()->is_platform_admin)->toBeTrue();
    $this->artisan('cms:platform-admin', ['email' => $this->owner->email, '--revoke' => true])->assertSuccessful();
    expect($this->owner->fresh()->is_platform_admin)->toBeFalse();
});

it('handles unavailable domains without partial completion or leaking another team', function (): void {
    app(SetupManager::class)->save($this->owner, $this->team, $this->website, true);
    auth()->logout();
    $second = Team::factory()->create(['user_id' => $this->owner->id]);
    $this->actingAs($this->owner);
    Filament::setTenant($second);
    Livewire::test(WebsiteSetup::class)->fillForm($this->website)->call('complete')->assertHasErrors(['data.domain']);
    expect(PanelSetup::where('team_id', $second->id)->exists())->toBeFalse();
    expect(Site::where('team_id', $second->id)->exists())->toBeFalse();
    expect(app(TenantContextInterface::class)->tenantId())->toBeNull();
});

it('requires platform administrators to enroll in two-factor authentication', function (): void {
    $this->owner->forceFill(['is_platform_admin' => true])->save();
    config(['two-factor.enforce' => true]);
    $this->get('/platform-admin')->assertRedirectContains('two-factor-setup');
});

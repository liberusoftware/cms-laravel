<?php

declare(strict_types=1);

use App\Models\ConnectedAccount;
use App\Models\Team;
use App\Models\User;
use App\Policies\ConnectedAccountPolicy;
use App\Providers\FortifyServiceProvider;
use App\Support\FilamentTenantResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Fortify;
use Liberu\Cms\Contracts\Media\MediaItemInterface;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;
use Liberu\Cms\Core\Http\Concerns\EmbedsFeaturedMedia;

uses(RefreshDatabase::class);

it('applies connected-account ownership policy decisions', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $owned = ConnectedAccount::factory()->for($owner)->create();
    $foreign = ConnectedAccount::factory()->for($other)->create();
    $policy = new ConnectedAccountPolicy;

    expect($policy->viewAny($owner))->toBeTrue()
        ->and($policy->create($owner))->toBeTrue()
        ->and($policy->view($owner, $owned))->toBeTrue()
        ->and($policy->update($owner, $owned))->toBeTrue()
        ->and($policy->delete($owner, $owned))->toBeTrue()
        ->and($policy->view($owner, $foreign))->toBeFalse()
        ->and($policy->update($owner, $foreign))->toBeFalse()
        ->and($policy->delete($owner, $foreign))->toBeFalse();
});

it('uses the API tenant before the panel tenant', function (): void {
    config()->set('permission.teams', true);
    app()->instance(TenantContextInterface::class, new class implements TenantContextInterface
    {
        public function tenantId(): int|string|null
        {
            return 123;
        }

        public function setTenantId(int|string|null $tenantId): void {}
    });

    $resolver = new FilamentTenantResolver;

    expect($resolver->tenantModel())->toBe(Team::class)
        ->and($resolver->currentTenantId())->toBe(123);
});

it('configures the Fortify passkey limiter with a credential or session fallback', function (): void {
    (new FortifyServiceProvider(app()))->boot();
    $limiter = RateLimiter::limiter('passkeys');

    $withCredential = Request::create('/login/passkeys', 'POST', ['credential' => ['id' => 'credential-1']]);
    $withSession = Request::create('/login/passkeys', 'POST');
    $withSession->setLaravelSession(app('session')->driver());

    expect($limiter($withCredential)->maxAttempts)->toBe(10)
        ->and($limiter($withSession)->maxAttempts)->toBe(10);
    expect(Fortify::username())->toBe('email');
});

it('embeds featured media with safe fallback fields', function (): void {
    $consumer = new class
    {
        use EmbedsFeaturedMedia;

        public function payload(?MediaItemInterface $media): ?array
        {
            return $this->featuredMediaPayload($media);
        }
    };
    $media = Mockery::mock(MediaItemInterface::class);
    $media->expects('url')->once()->andReturn('https://cdn.example.test/image.jpg');
    $media->expects('metadata')->once()->andReturn(['alt' => 'A photo']);

    expect($consumer->payload(null))->toBeNull()
        ->and($consumer->payload($media))->toBe([
            'url' => 'https://cdn.example.test/image.jpg',
            'alt' => 'A photo',
        ]);
});

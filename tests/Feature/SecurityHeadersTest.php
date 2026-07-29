<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Cms\Pages\Models\Page;

uses(RefreshDatabase::class);

function assertBaselineSecurityHeaders($response): void
{
    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Strict-Transport-Security');
}

it('adds the security headers to a public web response', function (): void {
    $user = User::factory()->create();
    Page::factory()->home()->create(['user_id' => $user->id]);

    $response = $this->get('/');

    $response->assertSuccessful();
    assertBaselineSecurityHeaders($response);
});

it('adds the security headers to the panel login page without breaking it', function (): void {
    $response = $this->get('/admin/login');

    $response->assertSuccessful();
    assertBaselineSecurityHeaders($response);
});

it('ships the content security policy in report-only mode so it cannot block panel assets', function (): void {
    $response = $this->get('/admin/login');

    $response->assertHeader('Content-Security-Policy-Report-Only')
        ->assertHeaderMissing('Content-Security-Policy');

    expect($response->headers->get('Content-Security-Policy-Report-Only'))
        ->toContain("default-src 'self'")
        ->toContain("frame-ancestors 'none'");
});

it('enforces the policy when report-only is disabled', function (): void {
    config()->set('security-headers.csp.report_only', false);

    $response = $this->get('/admin/login');

    $response->assertHeader('Content-Security-Policy')
        ->assertHeaderMissing('Content-Security-Policy-Report-Only');
});

it('omits HSTS when disabled', function (): void {
    config()->set('security-headers.hsts.enabled', false);

    $this->get('/admin/login')->assertHeaderMissing('Strict-Transport-Security');
});

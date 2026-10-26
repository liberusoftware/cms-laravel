<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CoreSetupSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SeederSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seeding_uses_configured_credentials_without_printing_them(): void
    {
        $this->app->instance('env', 'production');
        config(['app.seed_admin_password' => 'private-configured-seed-password']);
        ob_start();
        try {
            $exitCode = Artisan::call('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true]);
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        $this->assertSame(0, $exitCode);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('private-configured-seed-password', $admin->password));
        $this->assertStringNotContainsString('private-configured-seed-password', $output.Artisan::output());
        $this->assertStringNotContainsString('Admin password:', $output.Artisan::output());
    }

    public function test_unconfigured_administrator_password_is_not_predictable(): void
    {
        $this->app->instance('env', 'production');
        config(['app.seed_admin_password' => null]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $exitCode = Artisan::call('db:seed', ['--class' => CoreSetupSeeder::class, '--force' => true]);
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $this->assertSame(0, $exitCode);
        $this->assertFalse(Hash::check('password', $admin->password));
        $this->assertStringNotContainsString('Admin password:', Artisan::output());
    }
}

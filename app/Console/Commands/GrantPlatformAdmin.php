<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GrantPlatformAdmin extends Command
{
    protected $signature = 'cms:platform-admin {email : Existing user email} {--revoke : Remove platform access}';

    protected $description = 'Grant or revoke platform administrator access for an existing user';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->error('No user found with that email address.');

            return self::FAILURE;
        }
        $user->forceFill(['is_platform_admin' => ! $this->option('revoke')])->save();
        $this->info($this->option('revoke') ? 'Platform access revoked.' : 'Platform access granted. Sign in at /platform-admin.');

        return self::SUCCESS;
    }
}

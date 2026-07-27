<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $roleData = [
            'name' => 'super_admin',
            'guard_name' => 'web',
        ];

        // 2. Create Default Team (belongs to admin)
        if (Utils::isTenancyEnabled()) {
            $team = Team::create([
                'name' => 'Default',
                'personal_team' => false,
                'user_id' => $admin->id,
            ]);

            setPermissionsTeamId($team->id);
            $admin->teams()->syncWithoutDetaching([$team->id]);
            $roleData['team_id'] = $team->id;
        }

        $superAdminRole = Role::create($roleData);

        // 3. Assign super_admin role
        $admin->assignRole($superAdminRole);

        // 4. Materialise CMS permissions and map them onto the baseline roles so
        //    the admin (super_admin) can actually use the gated admin panel.
        $this->call(CmsBaselineRolesSeeder::class);
    }
}

<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Config\Repository;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Liberu\Cms\Core\CmsCoreServiceProvider;
use Liberu\Cms\Hello\HelloServiceProvider;
use Liberu\Cms\Users\Access\SyncPermissions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

/**
 * Materialise the module-declared CMS permissions and grant the given subset to
 * a user within their team's permission scope (via a dedicated test role). Used
 * by the Filament resource tests, which are gated by module-owned permissions.
 *
 * @param  array<int, string>  $permissions  Fully-qualified names, e.g. ["pages.view"].
 */
function grantCmsPermissions(User $user, Team $team, array $permissions): void
{
    setPermissionsTeamId($team->id);
    app(SyncPermissions::class)();

    $role = Role::firstOrCreate(['name' => 'cms-test', 'team_id' => $team->id, 'guard_name' => 'web']);
    $role->givePermissionTo($permissions);
    $user->syncRoles([$role]);

    setPermissionsTeamId($team->id);
}

/**
 * Boot the CMS kernel and the Hello module inside a fresh, bare Laravel
 * application that has none of this project's feature providers (Jetstream,
 * Filament, Socialstream, …). This is the embeddability harness: if the CMS
 * boots and serves here, it can be embedded inside any Laravel host.
 *
 * The facade application is repointed to the bare app only while its providers
 * boot, so module routes register into the bare app's router, then restored.
 *
 * @param  array<int, string>  $disabledModules
 */
function makeBareCmsApp(array $disabledModules = []): Application
{
    $original = Facade::getFacadeApplication();

    $app = new Application(sys_get_temp_dir().'/cms-bare-'.uniqid());
    $app->instance('files', new Filesystem);
    $app->instance('config', new Repository([
        'cms' => [
            'modules_enabled_by_default' => true,
            'disabled_modules' => $disabledModules,
        ],
    ]));

    $capsule = new Capsule;
    $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
    $app->instance(ConnectionResolverInterface::class, $capsule->getDatabaseManager());

    Facade::clearResolvedInstances();
    Facade::setFacadeApplication($app);

    try {
        $app->register(CmsCoreServiceProvider::class);
        $app->register(HelloServiceProvider::class);
        $app->boot();
    } finally {
        Facade::setFacadeApplication($original);
        Facade::clearResolvedInstances();
    }

    $app['router']->getRoutes()->refreshNameLookups();

    return $app;
}

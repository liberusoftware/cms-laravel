<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Enforce two-factor authentication for privileged roles
    |--------------------------------------------------------------------------
    |
    | When enabled, a signed-in user holding any of the privileged roles below
    | cannot use the admin panel until they enrol in two-factor authentication;
    | they are redirected to the 2FA setup page. Non-privileged users are
    | unaffected. Set TWO_FACTOR_ENFORCE=false to disable enforcement.
    |
    */

    'enforce' => (bool) env('TWO_FACTOR_ENFORCE', true),

    'privileged_roles' => ['super_admin', 'admin'],

];

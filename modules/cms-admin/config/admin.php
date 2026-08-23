<?php

declare(strict_types=1);

return [
    /*
     * The Filament navigation group under which every admin surface this module
     * ships is grouped. Keeping it in config lets a host rebrand the grouping
     * without touching module code.
     */
    'navigation_group' => 'CMS',

    /*
     * The guard whose permissions gate the admin surfaces. Mirrors cms-users so
     * authorization resolves against the same backend.
     */
    'guard' => 'web',
];

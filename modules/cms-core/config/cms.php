<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default module state
    |--------------------------------------------------------------------------
    |
    | When a module has no stored enable/disable decision yet, this is the
    | state it takes. Leaving this true means modules are on by default and
    | must be explicitly disabled.
    |
    */

    'modules_enabled_by_default' => true,

    /*
    |--------------------------------------------------------------------------
    | Force-disabled modules
    |--------------------------------------------------------------------------
    |
    | Module keys listed here are always disabled regardless of stored state.
    | This gives the host application (especially in embedded mode) a static,
    | config-driven kill switch that never touches another module's code.
    |
    */

    'disabled_modules' => [],

];

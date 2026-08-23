<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Cms\Observability\Http\Controllers\ReadinessController;

Route::get('/health/ready', ReadinessController::class)
    ->middleware('throttle:cms-observability')
    ->name('cms-observability.readiness');

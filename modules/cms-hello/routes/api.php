<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Cms\Hello\Http\Controllers\HelloController;

Route::get('api/v1/hello/{name?}', HelloController::class)->name('cms.hello.greet');

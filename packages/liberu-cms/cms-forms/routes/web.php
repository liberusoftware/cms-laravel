<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Cms\Forms\Http\Controllers\FormSubmissionController;

Route::post('/forms/{slug}', FormSubmissionController::class)
    ->middleware('throttle:cms-forms')
    ->name('cms-forms.submit');

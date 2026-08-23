<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\Cms\Seo\Http\Controllers\RobotsController;
use Liberu\Cms\Seo\Http\Controllers\SitemapController;

Route::get('/sitemap.xml', SitemapController::class)->name('cms-seo.sitemap');
Route::get('/robots.txt', RobotsController::class)->name('cms-seo.robots');

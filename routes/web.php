<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionItemController;
use App\Http\Controllers\PageController;
use App\Models\Collection;
use Illuminate\Support\Facades\Route;
use Liberu\Cms\Pages\Models\Page;

Route::get('/dashboard', function () {
    return redirect('/admin');
})->middleware(['auth'])->name('dashboard');

Route::get('/', [PageController::class, 'show']);
Route::get('/{collection:slug}/{item:slug}', [CollectionItemController::class, 'show']);

Route::get('/{slug}', function ($slug) {
    // 1. Try page
    if ($page = Page::where('slug', $slug)->first()) {
        return app(PageController::class)->show($slug);
    }

    // 2. Try collection
    if ($collection = Collection::where('slug', $slug)->first()) {
        return app(CollectionController::class)->show($collection);
    }

    abort(404);
})->name('pages.show');
require __DIR__.'/socialstream.php';

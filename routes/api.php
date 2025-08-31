<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchController;

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/search', [SearchController::class, 'search'])->name('api.search');
});
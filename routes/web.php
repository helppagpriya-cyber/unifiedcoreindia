<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteContactController;

Route::view('/', 'pages.home.home')->name('home');
Route::post('/contact', WebsiteContactController::class)
    ->middleware('throttle:5,1')
    ->name('website.contact.store');

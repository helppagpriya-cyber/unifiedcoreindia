<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteContactController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ErpSoftwarePackageController;

Route::view('/', 'pages.home.home')->name('home');
Route::post('/contact', WebsiteContactController::class)
    ->middleware('throttle:5,1')
    ->name('website.contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AccountController::class, 'create'])->name('register');
    Route::post('/register', [AccountController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AccountController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AccountController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AccountController::class, 'updatePassword'])->name('profile.password');
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
    Route::get('/erp-packages/versions/{version}', [ErpSoftwarePackageController::class, 'version'])
        ->name('erp-packages.version.download');
    Route::get('/erp-packages/modules/{module}', [ErpSoftwarePackageController::class, 'module'])
        ->name('erp-packages.module.download');
});

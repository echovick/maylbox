<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Landing', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('mail', function () {
    return Inertia::render('Mail');
})->middleware(['auth', 'verified'])->name('mail');

Route::get('account-setup', function () {
    return Inertia::render('AccountSetup', [
        'userEmail' => auth()->user()->email ?? '',
    ]);
})->middleware(['auth'])->name('account-setup');

// Email Account API routes
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('email-accounts', [\App\Http\Controllers\EmailAccountController::class, 'index']);
    Route::post('email-accounts', [\App\Http\Controllers\EmailAccountController::class, 'store']);
    Route::patch('email-accounts/{emailAccount}', [\App\Http\Controllers\EmailAccountController::class, 'update']);
    Route::delete('email-accounts/{emailAccount}', [\App\Http\Controllers\EmailAccountController::class, 'destroy']);
});

require __DIR__.'/settings.php';

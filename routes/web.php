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
    return Inertia::render('AccountSetup');
})->middleware(['auth', 'verified'])->name('account-setup');

require __DIR__.'/settings.php';

<?php

use App\Jobs\SyncEmailAccountJob;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Landing', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return redirect()->route('mail');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('mail', function () {
    $accounts = auth()->user()->emailAccounts()
        ->select(['id', 'name', 'email', 'type', 'provider', 'is_default', 'is_active', 'sync_status', 'last_synced_at', 'sync_error'])
        ->orderBy('is_default', 'desc')
        ->orderBy('created_at', 'asc')
        ->get();

    $defaultAccount = $accounts->firstWhere('is_default', true) ?? $accounts->first();

    // Auto-dispatch sync for accounts that were never synced
    $accounts->filter(fn($account) => $account->sync_status === 'pending' && $account->last_synced_at === null)
        ->each(fn($account) => SyncEmailAccountJob::dispatch($account));

    return Inertia::render('Mail', [
        'accounts'         => $accounts,
        'defaultAccountId' => $defaultAccount?->id,
    ]);
})->middleware(['auth', 'has.email.account'])->name('mail');

Route::get('account-setup', function () {
    if (Schema::hasTable('email_accounts') && auth()->user()->emailAccounts()->count() > 0) {
        return redirect()->route('mail');
    }

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

    // Folders
    Route::get('email-accounts/{emailAccount}/folders', [\App\Http\Controllers\FolderController::class, 'index']);

    // Emails
    Route::get('emails', [\App\Http\Controllers\EmailController::class, 'index']);
    Route::get('emails/{email}', [\App\Http\Controllers\EmailController::class, 'show']);
    Route::patch('emails/{email}', [\App\Http\Controllers\EmailController::class, 'update']);

    // Sync
    Route::post('email-accounts/{emailAccount}/sync', [\App\Http\Controllers\SyncController::class, 'sync']);
    Route::get('email-accounts/{emailAccount}/sync-status', [\App\Http\Controllers\SyncController::class, 'status']);
});

require __DIR__ . '/settings.php';

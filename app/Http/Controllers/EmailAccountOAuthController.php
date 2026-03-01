<?php

namespace App\Http\Controllers;

use App\Jobs\SyncEmailAccountJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class EmailAccountOAuthController extends Controller
{
    private const PROVIDER_CONFIG = [
        'google' => [
            'provider_name' => 'gmail',
            'scopes' => ['https://mail.google.com/'],
            'imap_host' => 'imap.gmail.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ],
        'microsoft' => [
            'provider_name' => 'outlook',
            'scopes' => [
                'https://outlook.office365.com/IMAP.AccessAsUser.All',
                'https://outlook.office365.com/SMTP.Send',
                'offline_access',
            ],
            'imap_host' => 'outlook.office365.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp-mail.outlook.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
        ],
    ];

    public function redirect(string $provider): RedirectResponse
    {
        $config = self::PROVIDER_CONFIG[$provider] ?? null;
        if (!$config) {
            abort(404);
        }

        $driver = Socialite::driver($provider);

        // For Google, override the redirect URI to the email-specific callback
        if ($provider === 'google') {
            $driver->redirectUrl(url(env('GOOGLE_EMAIL_REDIRECT_URI', '/email-accounts/oauth/google/callback')));
        }

        $driver->scopes($config['scopes']);

        // Request offline access to get a refresh token
        if ($provider === 'google') {
            $driver->with(['access_type' => 'offline', 'prompt' => 'consent']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $config = self::PROVIDER_CONFIG[$provider] ?? null;
        if (!$config) {
            abort(404);
        }

        try {
            $driver = Socialite::driver($provider);

            // For Google, override the redirect URI to match what was used in the redirect
            if ($provider === 'google') {
                $driver->redirectUrl(url(env('GOOGLE_EMAIL_REDIRECT_URI', '/email-accounts/oauth/google/callback')));
            }

            $socialUser = $driver->user();
        } catch (\Exception $e) {
            Log::warning("Email OAuth failed for {$provider}: {$e->getMessage()}");

            return redirect()->route('account-setup')
                ->with('error', 'Unable to connect with ' . ucfirst($config['provider_name']) . '. Please try again.');
        }

        $email = $socialUser->getEmail();
        $user = Auth::user();

        // Check if this email account already exists for this user
        $existingAccount = $user->emailAccounts()->where('email', $email)->first();
        if ($existingAccount) {
            // Update tokens on the existing account
            $existingAccount->update([
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken ?? $existingAccount->refresh_token,
                'token_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                'imap_password' => $socialUser->token,
                'smtp_password' => $socialUser->token,
                'sync_status' => 'pending',
                'sync_error' => null,
            ]);

            SyncEmailAccountJob::dispatch($existingAccount);

            return redirect()->route('mail');
        }

        // Set as default if this is the user's first account
        $isFirstAccount = $user->emailAccounts()->count() === 0;

        $account = $user->emailAccounts()->create([
            'name' => $socialUser->getName() ?: $email,
            'email' => $email,
            'type' => 'oauth',
            'provider' => $config['provider_name'],
            'access_token' => $socialUser->token,
            'refresh_token' => $socialUser->refreshToken,
            'token_expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
            'imap_host' => $config['imap_host'],
            'imap_port' => $config['imap_port'],
            'imap_encryption' => $config['imap_encryption'],
            'imap_username' => $email,
            'imap_password' => $socialUser->token,
            'smtp_host' => $config['smtp_host'],
            'smtp_port' => $config['smtp_port'],
            'smtp_encryption' => $config['smtp_encryption'],
            'smtp_username' => $email,
            'smtp_password' => $socialUser->token,
            'is_default' => $isFirstAccount,
            'sync_status' => 'pending',
        ]);

        SyncEmailAccountJob::dispatch($account);

        return redirect()->route('mail');
    }
}

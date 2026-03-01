<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const PROVIDERS = ['google', 'github'];

    public function redirect(string $provider): RedirectResponse
    {
        if (! in_array($provider, self::PROVIDERS)) {
            abort(404);
        }

        $driver = Socialite::driver($provider);

        if ($provider === 'github') {
            $driver->scopes(['user:email']);
        }

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        if (! in_array($provider, self::PROVIDERS)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            Log::warning("Social auth failed for {$provider}: {$e->getMessage()}");

            return redirect()->route('login')->with('status', 'Unable to authenticate with '.ucfirst($provider).'. Please try again.');
        }

        // 1. Check if social account already exists
        $socialAccount = SocialAccount::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $socialAccount->update([
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar_url' => $socialUser->getAvatar(),
            ]);

            Auth::login($socialAccount->user, remember: true);

            return redirect()->intended(route('dashboard'));
        }

        // 2. Check if a user exists with the same email
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            $existingUser->socialAccounts()->create([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'provider_token' => $socialUser->token,
                'provider_refresh_token' => $socialUser->refreshToken,
                'avatar_url' => $socialUser->getAvatar(),
            ]);

            Auth::login($existingUser, remember: true);

            return redirect()->intended(route('dashboard'));
        }

        // 3. Create a new user
        $user = User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? explode('@', $socialUser->getEmail())[0],
            'email' => $socialUser->getEmail(),
            'password' => null,
            'email_verified_at' => now(),
        ]);

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken,
            'avatar_url' => $socialUser->getAvatar(),
        ]);

        Auth::login($user, remember: true);

        return redirect()->route('account-setup');
    }
}

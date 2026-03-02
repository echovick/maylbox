<?php

namespace App\Services;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OAuthTokenService
{
    private const TOKEN_ENDPOINTS = [
        'gmail' => 'https://oauth2.googleapis.com/token',
        'outlook' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
    ];

    /**
     * Return a valid access token, refreshing if needed.
     */
    public function refreshIfNeeded(EmailAccount $account): string
    {
        // If token hasn't expired yet (with 5 min buffer), return current token
        if ($account->token_expires_at && $account->token_expires_at->isAfter(now()->addMinutes(5))) {
            return $account->access_token;
        }

        // If no refresh token available, return current access token as-is
        if (!$account->refresh_token) {
            return $account->access_token;
        }

        return $this->refresh($account);
    }

    private function refresh(EmailAccount $account): string
    {
        $endpoint = self::TOKEN_ENDPOINTS[$account->provider] ?? null;
        if (!$endpoint) {
            Log::warning("No token endpoint for provider: {$account->provider}");
            return $account->access_token;
        }

        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $account->refresh_token,
        ];

        if ($account->provider === 'gmail') {
            $params['client_id'] = config('services.google.client_id');
            $params['client_secret'] = config('services.google.client_secret');
        } elseif ($account->provider === 'outlook') {
            $params['client_id'] = config('services.microsoft.client_id');
            $params['client_secret'] = config('services.microsoft.client_secret');
        }

        $response = Http::asForm()->post($endpoint, $params);

        if (!$response->successful()) {
            Log::error("OAuth token refresh failed for account {$account->id}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return $account->access_token;
        }

        $data = $response->json();
        $newAccessToken = $data['access_token'];
        $expiresIn = $data['expires_in'] ?? 3600;

        $account->update([
            'access_token' => $newAccessToken,
            'token_expires_at' => now()->addSeconds($expiresIn),
            'imap_password' => $newAccessToken,
            'smtp_password' => $newAccessToken,
        ]);

        // Update refresh token if a new one was provided
        if (!empty($data['refresh_token'])) {
            $account->update(['refresh_token' => $data['refresh_token']]);
        }

        return $newAccessToken;
    }
}

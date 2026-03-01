<?php

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

function mockSocialiteUser(array $overrides = []): SocialiteUser
{
    $user = new SocialiteUser;
    $user->id = $overrides['id'] ?? '123456';
    $user->name = $overrides['name'] ?? 'Test User';
    $user->email = $overrides['email'] ?? 'test@example.com';
    $user->avatar = $overrides['avatar'] ?? 'https://example.com/avatar.jpg';
    $user->token = $overrides['token'] ?? 'test-token';
    $user->refreshToken = $overrides['refreshToken'] ?? 'test-refresh-token';
    $user->nickname = $overrides['nickname'] ?? 'testuser';

    return $user;
}

test('social redirect routes to provider', function () {
    $response = $this->get('/auth/google/redirect');

    $response->assertRedirectContains('accounts.google.com');
});

test('social redirect works for github', function () {
    $response = $this->get('/auth/github/redirect');

    $response->assertRedirectContains('github.com');
});

test('invalid provider returns 404', function () {
    $response = $this->get('/auth/invalid/redirect');

    $response->assertNotFound();
});

test('callback creates new user and social account for new user', function () {
    $socialiteUser = mockSocialiteUser([
        'email' => 'newuser@example.com',
        'name' => 'New User',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($socialiteUser)->getMock());

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticated();

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New User');
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->password)->toBeNull();

    expect(SocialAccount::where('provider', 'google')->where('provider_id', '123456')->exists())->toBeTrue();

    $response->assertRedirect(route('account-setup'));
});

test('callback links social account to existing user with same email', function () {
    $user = User::factory()->create(['email' => 'existing@example.com']);

    $socialiteUser = mockSocialiteUser([
        'email' => 'existing@example.com',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($socialiteUser)->getMock());

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticated();
    expect(auth()->id())->toBe($user->id);
    expect($user->socialAccounts()->where('provider', 'google')->exists())->toBeTrue();

    $response->assertRedirect(route('dashboard'));
});

test('callback logs in existing social account user and updates tokens', function () {
    $user = User::factory()->create();
    $socialAccount = SocialAccount::create([
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_id' => '123456',
        'provider_token' => 'old-token',
        'provider_refresh_token' => 'old-refresh-token',
    ]);

    $socialiteUser = mockSocialiteUser([
        'token' => 'new-token',
        'refreshToken' => 'new-refresh-token',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($socialiteUser)->getMock());

    $response = $this->get('/auth/google/callback');

    $this->assertAuthenticated();
    expect(auth()->id())->toBe($user->id);

    $socialAccount->refresh();
    expect($socialAccount->provider_token)->toBe('new-token');
    expect($socialAccount->provider_refresh_token)->toBe('new-refresh-token');

    $response->assertRedirect(route('dashboard'));
});

test('callback redirects to login with error when oauth fails', function () {
    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andThrow(new \Exception('OAuth failed'))->getMock());

    $response = $this->get('/auth/google/callback');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');
});

test('user can link multiple social providers', function () {
    $user = User::factory()->create(['email' => 'multi@example.com']);

    // Link Google
    $googleUser = mockSocialiteUser([
        'id' => 'google-id-123',
        'email' => 'multi@example.com',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->once()
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($googleUser)->getMock());

    $this->get('/auth/google/callback');
    auth()->logout();

    // Link GitHub
    $githubUser = mockSocialiteUser([
        'id' => 'github-id-456',
        'email' => 'multi@example.com',
    ]);

    Socialite::shouldReceive('driver')
        ->with('github')
        ->once()
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($githubUser)->getMock());

    $this->get('/auth/github/callback');

    expect($user->socialAccounts()->count())->toBe(2);
    expect($user->socialAccounts()->where('provider', 'google')->exists())->toBeTrue();
    expect($user->socialAccounts()->where('provider', 'github')->exists())->toBeTrue();
});

test('social-only user has no password', function () {
    $socialiteUser = mockSocialiteUser([
        'email' => 'social-only@example.com',
    ]);

    Socialite::shouldReceive('driver')
        ->with('google')
        ->andReturn(Mockery::mock()->shouldReceive('user')->andReturn($socialiteUser)->getMock());

    $this->get('/auth/google/callback');

    $user = User::where('email', 'social-only@example.com')->first();
    expect($user->hasPassword())->toBeFalse();
});

test('authenticated users cannot access social auth routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/auth/google/redirect');

    $response->assertRedirect(route('dashboard'));
});

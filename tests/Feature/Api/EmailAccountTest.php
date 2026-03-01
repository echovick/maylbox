<?php

use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SyncEmailAccountJob;

test('guests cannot access email accounts', function () {
    $this->getJson('/api/email-accounts')->assertUnauthorized();
});

test('user can list their email accounts', function () {
    $user = User::factory()->create();
    EmailAccount::factory()->default()->create(['user_id' => $user->id]);
    EmailAccount::factory()->create(['user_id' => $user->id]);

    // Another user's account should not appear
    EmailAccount::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/email-accounts');

    $response->assertOk()
        ->assertJsonCount(2);
});

test('user can create an imap email account', function () {
    Bus::fake();

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/email-accounts', [
        'name' => 'Work Email',
        'email' => 'work@example.com',
        'type' => 'imap',
        'provider' => 'custom',
        'imap_host' => 'imap.example.com',
        'imap_port' => 993,
        'imap_encryption' => 'ssl',
        'imap_password' => 'secret123',
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 465,
        'smtp_encryption' => 'ssl',
        'smtp_password' => 'secret123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('account.email', 'work@example.com')
        ->assertJsonPath('account.is_default', true);

    $this->assertDatabaseHas('email_accounts', [
        'user_id' => $user->id,
        'email' => 'work@example.com',
        'type' => 'imap',
    ]);

    Bus::assertDispatched(SyncEmailAccountJob::class);
});

test('first account is automatically set as default', function () {
    Bus::fake();

    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/email-accounts', [
        'name' => 'First Account',
        'email' => 'first@example.com',
        'type' => 'imap',
        'imap_host' => 'imap.example.com',
        'imap_port' => 993,
        'imap_password' => 'secret',
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 465,
        'smtp_password' => 'secret',
    ]);

    $this->actingAs($user)->postJson('/api/email-accounts', [
        'name' => 'Second Account',
        'email' => 'second@example.com',
        'type' => 'imap',
        'imap_host' => 'imap.example.com',
        'imap_port' => 993,
        'imap_password' => 'secret',
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 465,
        'smtp_password' => 'secret',
    ]);

    $accounts = $user->emailAccounts()->orderBy('id')->get();
    expect($accounts[0]->is_default)->toBeTrue();
    expect($accounts[1]->is_default)->toBeFalse();
});

test('creating account with duplicate email fails', function () {
    Bus::fake();

    $user = User::factory()->create();
    EmailAccount::factory()->create(['user_id' => $user->id, 'email' => 'taken@example.com']);

    $response = $this->actingAs($user)->postJson('/api/email-accounts', [
        'name' => 'Duplicate',
        'email' => 'taken@example.com',
        'type' => 'imap',
        'imap_host' => 'imap.example.com',
        'imap_port' => 993,
        'imap_password' => 'secret',
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 465,
        'smtp_password' => 'secret',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

test('creating account requires imap fields for imap type', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/email-accounts', [
        'name' => 'Missing Fields',
        'email' => 'test@example.com',
        'type' => 'imap',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['imap_host', 'imap_port', 'imap_password', 'smtp_host', 'smtp_port', 'smtp_password']);
});

test('user can update their email account', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

    $response = $this->actingAs($user)->patchJson("/api/email-accounts/{$account->id}", [
        'name' => 'New Name',
    ]);

    $response->assertOk()
        ->assertJsonPath('account.name', 'New Name');

    expect($account->refresh()->name)->toBe('New Name');
});

test('setting account as default unsets other defaults', function () {
    $user = User::factory()->create();
    $first = EmailAccount::factory()->default()->create(['user_id' => $user->id]);
    $second = EmailAccount::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->patchJson("/api/email-accounts/{$second->id}", [
        'is_default' => true,
    ]);

    expect($first->refresh()->is_default)->toBeFalse();
    expect($second->refresh()->is_default)->toBeTrue();
});

test('updating credentials re-triggers sync', function () {
    Bus::fake();

    $user = User::factory()->create();
    $account = EmailAccount::factory()->create([
        'user_id' => $user->id,
        'sync_status' => 'synced',
    ]);

    $this->actingAs($user)->patchJson("/api/email-accounts/{$account->id}", [
        'imap_password' => 'new-password',
    ]);

    expect($account->refresh()->sync_status)->toBe('pending');
    Bus::assertDispatched(SyncEmailAccountJob::class);
});

test('user cannot update another users account', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();

    $response = $this->actingAs($user)->patchJson("/api/email-accounts/{$otherAccount->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertForbidden();
});

test('user can delete their email account', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/email-accounts/{$account->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('email_accounts', ['id' => $account->id]);
});

test('user cannot delete another users account', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/email-accounts/{$otherAccount->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('email_accounts', ['id' => $otherAccount->id]);
});

test('sensitive fields are hidden in account list response', function () {
    $user = User::factory()->create();
    EmailAccount::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/email-accounts');

    $response->assertOk();
    $account = $response->json()[0];
    expect($account)->not->toHaveKey('access_token');
    expect($account)->not->toHaveKey('refresh_token');
    expect($account)->not->toHaveKey('imap_password');
    expect($account)->not->toHaveKey('smtp_password');
});

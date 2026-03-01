<?php

use App\Jobs\SyncEmailAccountJob;
use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

test('guests cannot trigger sync', function () {
    $account = EmailAccount::factory()->create();

    $this->postJson("/api/email-accounts/{$account->id}/sync")->assertUnauthorized();
});

test('user can trigger sync for their account', function () {
    Bus::fake();

    $user = User::factory()->create();
    $account = EmailAccount::factory()->create([
        'user_id' => $user->id,
        'sync_status' => 'synced',
    ]);

    $response = $this->actingAs($user)->postJson("/api/email-accounts/{$account->id}/sync");

    $response->assertOk()
        ->assertJsonPath('sync_status', 'pending');

    expect($account->refresh()->sync_status)->toBe('pending');
    Bus::assertDispatched(SyncEmailAccountJob::class);
});

test('sync is not re-triggered if already syncing', function () {
    Bus::fake();

    $user = User::factory()->create();
    $account = EmailAccount::factory()->syncing()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson("/api/email-accounts/{$account->id}/sync");

    $response->assertOk()
        ->assertJsonPath('sync_status', 'syncing')
        ->assertJsonPath('message', 'Sync already in progress');

    Bus::assertNotDispatched(SyncEmailAccountJob::class);
});

test('user cannot trigger sync for another users account', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();

    $response = $this->actingAs($user)->postJson("/api/email-accounts/{$otherAccount->id}/sync");

    $response->assertForbidden();
});

test('user can check sync status', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create([
        'user_id' => $user->id,
        'sync_status' => 'synced',
        'last_synced_at' => now()->subMinutes(5),
    ]);

    $response = $this->actingAs($user)->getJson("/api/email-accounts/{$account->id}/sync-status");

    $response->assertOk()
        ->assertJsonPath('sync_status', 'synced')
        ->assertJsonStructure(['sync_status', 'last_synced_at', 'sync_error']);
});

test('sync status shows error for failed accounts', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->failed()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/email-accounts/{$account->id}/sync-status");

    $response->assertOk()
        ->assertJsonPath('sync_status', 'failed')
        ->assertJsonPath('sync_error', 'Connection refused');
});

test('user cannot check sync status for another users account', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/email-accounts/{$otherAccount->id}/sync-status");

    $response->assertForbidden();
});

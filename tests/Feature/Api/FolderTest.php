<?php

use App\Models\EmailAccount;
use App\Models\Folder;
use App\Models\User;

test('guests cannot access folders', function () {
    $account = EmailAccount::factory()->create();

    $this->getJson("/api/email-accounts/{$account->id}/folders")->assertUnauthorized();
});

test('user can list folders for their account', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);

    Folder::factory()->create(['email_account_id' => $account->id, 'type' => 'inbox', 'name' => 'INBOX']);
    Folder::factory()->sent()->create(['email_account_id' => $account->id]);
    Folder::factory()->trash()->create(['email_account_id' => $account->id]);

    $response = $this->actingAs($user)->getJson("/api/email-accounts/{$account->id}/folders");

    $response->assertOk()
        ->assertJsonCount(3);
});

test('folders are sorted by type priority', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);

    // Create in reverse order
    Folder::factory()->trash()->create(['email_account_id' => $account->id]);
    Folder::factory()->sent()->create(['email_account_id' => $account->id]);
    Folder::factory()->create(['email_account_id' => $account->id, 'type' => 'inbox', 'name' => 'INBOX']);

    $response = $this->actingAs($user)->getJson("/api/email-accounts/{$account->id}/folders");

    $response->assertOk();
    $folders = $response->json();

    // inbox should come first, then sent, then trash
    expect($folders[0]['type'])->toBe('inbox');
    expect($folders[1]['type'])->toBe('sent');
    expect($folders[2]['type'])->toBe('trash');
});

test('user cannot access folders of another users account', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();
    Folder::factory()->create(['email_account_id' => $otherAccount->id]);

    $response = $this->actingAs($user)->getJson("/api/email-accounts/{$otherAccount->id}/folders");

    $response->assertForbidden();
});

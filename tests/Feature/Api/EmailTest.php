<?php

use App\Models\Email;
use App\Models\EmailAccount;
use App\Models\Folder;
use App\Models\User;

test('guests cannot access emails', function () {
    $this->getJson('/api/emails')->assertUnauthorized();
});

test('user can list emails in a folder', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create(['email_account_id' => $account->id]);

    Email::factory()->count(3)->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
    ]);

    $response = $this->actingAs($user)->getJson("/api/emails?account_id={$account->id}&folder_id={$folder->id}");

    $response->assertOk()
        ->assertJsonPath('total', 3)
        ->assertJsonCount(3, 'data');
});

test('emails are paginated at 20 per page', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create(['email_account_id' => $account->id]);

    Email::factory()->count(25)->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
    ]);

    $response = $this->actingAs($user)->getJson("/api/emails?account_id={$account->id}&folder_id={$folder->id}");

    $response->assertOk()
        ->assertJsonPath('total', 25)
        ->assertJsonPath('per_page', 20)
        ->assertJsonCount(20, 'data');

    // Page 2
    $page2 = $this->actingAs($user)->getJson("/api/emails?account_id={$account->id}&folder_id={$folder->id}&page=2");

    $page2->assertOk()
        ->assertJsonCount(5, 'data');
});

test('emails are ordered by date descending', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create(['email_account_id' => $account->id]);

    Email::factory()->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
        'subject' => 'Older email',
        'date' => now()->subDays(2),
    ]);

    Email::factory()->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
        'subject' => 'Newer email',
        'date' => now(),
    ]);

    $response = $this->actingAs($user)->getJson("/api/emails?account_id={$account->id}&folder_id={$folder->id}");

    $response->assertOk();
    $emails = $response->json('data');
    expect($emails[0]['subject'])->toBe('Newer email');
    expect($emails[1]['subject'])->toBe('Older email');
});

test('listing emails requires account_id and folder_id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/emails');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['account_id', 'folder_id']);
});

test('user cannot list emails from another users account', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();
    $folder = Folder::factory()->create(['email_account_id' => $otherAccount->id]);

    $response = $this->actingAs($user)->getJson("/api/emails?account_id={$otherAccount->id}&folder_id={$folder->id}");

    $response->assertForbidden();
});

test('user can view a single email', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create(['email_account_id' => $account->id]);
    $email = Email::factory()->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
        'subject' => 'Test Subject',
    ]);

    $response = $this->actingAs($user)->getJson("/api/emails/{$email->id}");

    $response->assertOk()
        ->assertJsonPath('subject', 'Test Subject')
        ->assertJsonPath('body_html', $email->body_html);
});

test('user cannot view another users email', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();
    $folder = Folder::factory()->create(['email_account_id' => $otherAccount->id]);
    $email = Email::factory()->create([
        'email_account_id' => $otherAccount->id,
        'folder_id' => $folder->id,
    ]);

    $response = $this->actingAs($user)->getJson("/api/emails/{$email->id}");

    $response->assertForbidden();
});

test('user can mark email as read', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create([
        'email_account_id' => $account->id,
        'unread_count' => 1,
    ]);
    $email = Email::factory()->unread()->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
    ]);

    $response = $this->actingAs($user)->patchJson("/api/emails/{$email->id}", [
        'is_read' => true,
    ]);

    $response->assertOk();
    expect($email->refresh()->is_read)->toBeTrue();
    expect($folder->refresh()->unread_count)->toBe(0);
});

test('user can star an email', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create(['email_account_id' => $account->id]);
    $email = Email::factory()->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
        'is_starred' => false,
    ]);

    $response = $this->actingAs($user)->patchJson("/api/emails/{$email->id}", [
        'is_starred' => true,
    ]);

    $response->assertOk();
    expect($email->refresh()->is_starred)->toBeTrue();
});

test('marking email as read updates folder unread count', function () {
    $user = User::factory()->create();
    $account = EmailAccount::factory()->create(['user_id' => $user->id]);
    $folder = Folder::factory()->create([
        'email_account_id' => $account->id,
        'unread_count' => 3,
    ]);

    $unreadEmail = Email::factory()->unread()->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
    ]);

    Email::factory()->unread()->count(2)->create([
        'email_account_id' => $account->id,
        'folder_id' => $folder->id,
    ]);

    $this->actingAs($user)->patchJson("/api/emails/{$unreadEmail->id}", [
        'is_read' => true,
    ]);

    expect($folder->refresh()->unread_count)->toBe(2);
});

test('user cannot update another users email', function () {
    $user = User::factory()->create();
    $otherAccount = EmailAccount::factory()->create();
    $folder = Folder::factory()->create(['email_account_id' => $otherAccount->id]);
    $email = Email::factory()->create([
        'email_account_id' => $otherAccount->id,
        'folder_id' => $folder->id,
    ]);

    $response = $this->actingAs($user)->patchJson("/api/emails/{$email->id}", [
        'is_read' => true,
    ]);

    $response->assertForbidden();
});

<?php

use App\Models\EmailAccount;
use App\Models\User;

test('guests are redirected to login from mail page', function () {
    $response = $this->get(route('mail'));

    $response->assertRedirect(route('login'));
});

test('users without email accounts are redirected to account setup', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('mail'));

    $response->assertRedirect(route('account-setup'));
});

test('users with email accounts can access mail page', function () {
    $user = User::factory()->create();
    EmailAccount::factory()->default()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('mail'));

    $response->assertOk();
});

test('account setup page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('account-setup'));

    $response->assertOk();
});

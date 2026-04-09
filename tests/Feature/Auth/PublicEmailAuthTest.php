<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('public login screen can be rendered', function () {
    $this->get(route('public.login'))->assertOk();
});

test('public register screen can be rendered', function () {
    $this->get(route('public.register'))->assertOk();
});

test('public users can register and are redirected to verification notice', function () {
    Event::fake();

    $response = $this->post(route('public.register.store'), [
        'name' => 'Public User',
        'email' => 'public-user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticated();
    Event::assertDispatched(Registered::class);
});

test('unverified public users are redirected to verification notice after login', function () {
    $user = User::factory()->unverified()->create([
        'is_admin' => false,
    ]);

    $response = $this->post(route('public.login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user);
});

test('verified public users are redirected home after login', function () {
    $user = User::factory()->create([
        'is_admin' => false,
        'email_verified_at' => now(),
    ]);

    $response = $this->post(route('public.login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('home', absolute: false));
    $this->assertAuthenticatedAs($user);
});

test('public users can verify email from signed link', function () {
    $user = User::factory()->unverified()->create([
        'is_admin' => false,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ],
    );

    $this->actingAs($user)
        ->get($verificationUrl)
        ->assertRedirect(route('home', absolute: false));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

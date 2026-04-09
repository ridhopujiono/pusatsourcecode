<?php

use App\Models\User;

test('guest users are redirected to the admin login screen', function () {
    $response = $this->get('/admin/products');

    $response->assertRedirect('/login');
});

test('non admin users can not access the admin product index', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this
        ->actingAs($user)
        ->get('/admin/products');

    $response->assertForbidden();
});

test('admin users can access the admin product index', function () {
    $user = User::factory()->create(['is_admin' => true]);

    $response = $this
        ->actingAs($user)
        ->get('/admin/products');

    $response->assertOk();
});

test('dashboard route redirects admin users to the product index', function () {
    $user = User::factory()->create(['is_admin' => true]);

    $response = $this
        ->actingAs($user)
        ->get('/dashboard');

    $response->assertRedirect(route('admin.products.index'));
});

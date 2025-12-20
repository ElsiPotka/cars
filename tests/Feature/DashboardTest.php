<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    if (! Role::where('name', 'admin')->exists()) {
        Role::create(['name' => 'admin']);
    }
});

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});

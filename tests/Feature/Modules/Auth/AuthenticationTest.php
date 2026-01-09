<?php

use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('users can authenticate using the login endpoint', function () {
    $user = User::factory()->create([
        'password' => bcrypt($password = 'password'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => $password,
    ]);

    $response->assertStatus(200); 
    
    $response->assertJsonStructure(['token']); 
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
                     ->postJson('/api/auth/logout');

    $response->assertStatus(200);
});
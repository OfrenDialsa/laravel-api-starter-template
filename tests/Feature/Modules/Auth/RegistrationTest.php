<?php

test('new users can register', function () {
    $response = $this->post('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});
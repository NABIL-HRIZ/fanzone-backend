<?php

test('new users can register', function () {
    $response = $this->postJson('/register', [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '0612345678',
    ]);

    
   

    
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]);


});

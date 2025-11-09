<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login and receive token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'user' => ['id', 'first_name', 'last_name', 'email'],
                 'token',
                 'role'
             ]);

   
});

test('user cannot login with wrong password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertStatus(422);
});

test('user can logout', function () {
   
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    
    $loginResponse = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $loginResponse->assertStatus(200);
    $token = $loginResponse->json('token');

    
    $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                           ->postJson('/api/logout');

    $logoutResponse->assertStatus(200)
                   ->assertJson([
                       'message' => 'Logged out successfully.'
                   ]);

   
});

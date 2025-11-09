<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

use Illuminate\Support\Facades\Hash;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_get_all_users()
    {
        Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/show-fans');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => ['id', 'first_name', 'last_name', 'email', 'phone', 'roles']
        ]);
    }

    /** @test */
    public function admin_can_create_a_new_fan()
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'fan']); 

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $fanData = [
            'first_name' => 'Nabil',
            'last_name' => 'Hariz',
            'email' => 'fan@example.com',
            'phone' => '0612345678',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/add-fan', $fanData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'created_at',
                'updated_at',
            ],
            'role',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fan@example.com',
            'first_name' => 'Nabil',
            'last_name' => 'Hariz',
            'phone' => '0612345678',
        ]);

        $user = User::where('email', 'fan@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));

        $this->assertTrue($user->hasRole('fan'));
    }

    
}

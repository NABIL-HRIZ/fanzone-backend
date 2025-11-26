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

   

    
}

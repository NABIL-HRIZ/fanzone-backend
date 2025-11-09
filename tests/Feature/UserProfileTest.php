<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

use Illuminate\Support\Facades\Hash;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'fan']);
        Role::firstOrCreate(['name' => 'agent']);
    }

    /** @test */
    public function user_can_view_their_profile()
    {
        $user = User::factory()->create();
        $user->syncRoles(['fan']); 

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/profile');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $user->id,
                     'email' => $user->email,
                 ]);
    }

    /** @test */
    public function user_can_update_their_profile()
    {
        $user = User::factory()->create([
            'first_name' => 'OldFirst',
            'last_name' => 'OldLast',
            'phone' => '0612345678',
        ]);
        $user->syncRoles(['agent']); 

        $updateData = [
            'first_name' => 'NewFirst',
            'last_name' => 'NewLast',
            'phone' => '0698765432',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->putJson('/api/profile', $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'first_name' => 'NewFirst',
                     'last_name' => 'NewLast',
                     'phone' => '0698765432',
                 ]);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function guest_cannot_access_profile_routes()
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);

        $response = $this->putJson('/api/profile', []);
        $response->assertStatus(401);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_all_users()
    {
        // 1. Créer un rôle admin
        $adminRole = Role::create(['name' => 'admin']);

        // 2. Créer un utilisateur admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // 3. Créer quelques utilisateurs "normaux"
        User::factory()->count(3)->create();

        // 4. Se connecter comme admin et appeler l'API
        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/show-fans');

        // 5. Vérifier le statut 200
        $response->assertStatus(200);

        // 6. Vérifier que JSON contient les champs attendus
        $response->assertJsonStructure([
            '*' => ['id', 'first_name', 'last_name', 'email', 'phone', 'roles']
        ]);
    }
}

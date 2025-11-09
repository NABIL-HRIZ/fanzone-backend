<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Matche;
use App\Models\Zone;

class MatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_all_matches()
    {
        $match1 = Matche::factory()->create(['match_date' => now()->addDays(1)]);
        $match2 = Matche::factory()->create(['match_date' => now()->addDays(2)]);

        Zone::factory()->create([
            'matche_id' => $match1->id,
            'name' => 'Zone VIP',
        ]);

        Zone::factory()->create([
            'matche_id' => $match2->id,
            'name' => 'Zone Standard',
        ]);

        $response = $this->getJson('/api/show-matches');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id','team_one_title','team_one_image','team_two_title','team_two_image','match_date','stadium','description','created_at','updated_at','zones']
            ]
        ]);

        $this->assertDatabaseHas('matches', ['id' => $match1->id]);
        $this->assertDatabaseHas('matches', ['id' => $match2->id]);

        $this->assertDatabaseHas('zones', ['matche_id' => $match1->id, 'name' => 'Zone VIP']);
        $this->assertDatabaseHas('zones', ['matche_id' => $match2->id, 'name' => 'Zone Standard']);
    }

    /** @test */
    public function admin_can_create_a_match()
    {
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $matchData = [
            'team_one_title' => 'Team A',
            'team_one_image' => 'team_a.png',
            'team_two_title' => 'Team B',
            'team_two_image' => 'team_b.png',
            'match_date'     => now()->addDays(5)->toDateTimeString(),
            'stadium'        => 'Stadium XYZ',
            'description'    => 'Big match',
        ];

        // 3. POST request as admin
        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/add-match', $matchData);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message',
            'match' => [
                'id',
                'team_one_title',
                'team_one_image',
                'team_two_title',
                'team_two_image',
                'match_date',
                'stadium',
                'description',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('matches', [
            'team_one_title' => 'Team A',
            'team_two_title' => 'Team B',
            'stadium'        => 'Stadium XYZ',
        ]);
    }
}

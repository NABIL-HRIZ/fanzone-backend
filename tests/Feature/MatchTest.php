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

  
}

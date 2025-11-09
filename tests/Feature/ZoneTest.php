<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Zone;
use App\Models\Matche;
use App\Models\User;
use App\Models\Role;


class ZoneTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_all_zones()
    {
        $match1 = Matche::factory()->create(['match_date' => now()->addDays(1)]);
        $match2 = Matche::factory()->create(['match_date' => now()->addDays(2)]);

        $zone1 = Zone::factory()->create([
            'matche_id' => $match1->id,
            'name' => 'VIP Zone',
        ]);

        $zone2 = Zone::factory()->create([
            'matche_id' => $match2->id,
            'name' => 'Standard Zone',
        ]);

        $response = $this->getJson('/api/show-zones');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'matche_id',
                    'name',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);

        $this->assertDatabaseHas('zones', ['id' => $zone1->id, 'name' => 'VIP Zone']);
        $this->assertDatabaseHas('zones', ['id' => $zone2->id, 'name' => 'Standard Zone']);
    }

   
}

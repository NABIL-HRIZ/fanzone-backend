<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Matche;
use App\Models\Zone;
use App\Models\Reservation;
use App\Models\Scan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

         


       $user =User::factory()->create([
    'first_name' => 'admin',
    'last_name'  => '01',
    'email'      => 'admin@test.ma',
    'phone'      => '0609153426',
    'password'   => \Illuminate\Support\Facades\Hash::make('password'),
]);

        $user->syncRoles(['admin']); 

         $this->call(RoleSeeder::class);

         $users=User::factory(10)->create();
       
  $users->each(function ($user) {
        $user->syncRoles(['fan']); 
    });
        Matche::factory(10)->create();

        Zone::factory(10)->create();

        Reservation::factory(10)->create();

        Scan::factory(10)->create();
        



    }

}

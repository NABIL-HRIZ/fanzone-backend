<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fanRole = Role::firstOrCreate(
    ['name' => 'fan'],
    ['display_name' => 'fan', 'description' => 'un spectateur normal  ']
);
$agentRole = Role::firstOrCreate(
    ['name' => 'agent'],
    ['display_name' => 'agent', 'description' => 'agent de sécurité ']
);
$adminRole = Role::firstOrCreate(
    ['name' => 'admin'],
    ['display_name' => 'Administrateur', 'description' => 'Full access admin']
);

    }
}
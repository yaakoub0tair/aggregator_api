<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SourceSeeder::class,
        ]);

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'role_id' => 2,
        ]);

        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@test.com',
            'password' => 'password123',
            'role_id'  => 1,
        ]);
    }
}

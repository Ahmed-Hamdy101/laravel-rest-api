<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Core independent tables first
            PermissionSeeder::class, // <-- Must be first!
            RoleSeeder::class,       // <-- Can now safely look up and bind permissions
            
            // 2. Data dependencies next
            userSeeder::class,       // <-- Assigns roles to users
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            ['name' => 'View Users'], // Fixed typo: 'Viwe' -> 'View'
            ['name' => 'Edit Users'],
            ['name' => 'View Roles'],
            ['name' => 'Edit Roles'],
            ['name' => 'View Products'],
            ['name' => 'Edit Products'],
            ['name' => 'View Orders'],
            ['name' => 'Edit Orders'],
        ]);
    }
}
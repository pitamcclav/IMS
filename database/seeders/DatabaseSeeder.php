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
        // First run the roles seeder so roles exist before creating staff
        $this->call([
            RolesSeeder::class,
            StaffSeeder::class,
        ]);
    }
}

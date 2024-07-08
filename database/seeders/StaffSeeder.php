<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run()
    {
        DB::table('staff')->insert([
            'staffName' => 'Goodman',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make(12345678), // Replace 'password' with actual password
            'created_at' => now(),
            'updated_at' => now(),
            // other fields
        ]);
    }
}

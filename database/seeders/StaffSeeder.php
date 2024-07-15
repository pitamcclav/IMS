<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Staff;

class StaffSeeder extends Seeder
{
    public function run()
    {
        // Insert the default admin user
        $admin = Staff::create([
            'staffName' => 'Goodman',
            'email' => 'admin@example.com',
            'password' => Hash::make(12345678), // Replace '12345678' with the actual password
            'created_at' => now(),
            'updated_at' => now(),
            // other fields
        ]);

        // Assign the super-admin role to the admin user
        $adminRole = Role::findByName('admin', 'staff');
        $admin->assignRole($adminRole);
    }
}


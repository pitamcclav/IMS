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
            'staffName' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'), // Replace '12345678' with the actual password
            'created_at' => now(),
            'updated_at' => now(),
            // other fields
        ]);

        // Assign the super-admin role to the admin user
        $adminRole = Role::findByName('admin', 'staff');
        $admin->syncRoles($adminRole);
    }
}


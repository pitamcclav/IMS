<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't already exist
        $roles = [
            'admin',
            'staff',
            'manager',
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'staff']);
            // Optionally, you can assign default permissions to roles here if needed
        }

        // Create permissions if they don't already exist
        $permissions = [
            'manage roles and permissions',
            'manage staff',
            'manage stores',
            'manage requests',
            'manage items',
            'manage categories',
            'manage inventory',
            'manage suppliers',
            'manage order limits',
            'generate reports',
            'view requests',
            'view items',
            'view categories',
            'view inventory',
            'view suppliers',
            'view order limits',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'staff']);
            // Optionally, you can assign default roles to permissions here if needed
        }

        // Assign permissions to roles

        $admin = Role::findByName('admin','staff');
        $staff = Role::findByName('staff','staff');
        $manager = Role::findByName('manager','staff');


        // Admin has all permissions
        $admin->syncPermissions(Permission::all());


        // Manager has specific permissions
        $manager->syncPermissions([
            'manage requests',
            'manage items',
            'manage categories',
            'manage inventory',
            'manage suppliers',
            'manage order limits',
            'generate reports',
        ]);

        // Staff has limited permissions
        $staff->syncPermissions([
            'manage requests',
        ]);

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // User
        Permission::create(['name' => 'users.index']);
        Permission::create(['name' => 'users.show']);
        Permission::create(['name' => 'users.store']);
        Permission::create(['name' => 'users.update']);
        Permission::create(['name' => 'users.destroy']);

        // Users
        $customer = Role::create(['name' => 'customer']);
        $admin = Role::create(['name' => 'admin']);
 
 
        // Assigning Permissions Customer
        $customer->syncPermissions([
            // AUTH
            'users.update'
        ]);       
 
        // Assigning Permissions Admin
        $admin->syncPermissions([
            // AUTH
            'users.index', 'users.show', 'users.store', 'users.update', 'users.destroy',
        ]);       
    }
}

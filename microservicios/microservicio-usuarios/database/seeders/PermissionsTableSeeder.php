<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User permissions
        $viewUsers = Permission::create([
            'name' => 'view-users',
            'display_name' => 'View Users',
            'description' => 'Can view user list and details',
        ]);

        $createUsers = Permission::create([
            'name' => 'create-users',
            'display_name' => 'Create Users',
            'description' => 'Can create new users',
        ]);

        $editUsers = Permission::create([
            'name' => 'edit-users',
            'display_name' => 'Edit Users',
            'description' => 'Can edit existing users',
        ]);

        $deleteUsers = Permission::create([
            'name' => 'delete-users',
            'display_name' => 'Delete Users',
            'description' => 'Can delete users',
        ]);

        // Role permissions
        $viewRoles = Permission::create([
            'name' => 'view-roles',
            'display_name' => 'View Roles',
            'description' => 'Can view role list and details',
        ]);

        $createRoles = Permission::create([
            'name' => 'create-roles',
            'display_name' => 'Create Roles',
            'description' => 'Can create new roles',
        ]);

        $editRoles = Permission::create([
            'name' => 'edit-roles',
            'display_name' => 'Edit Roles',
            'description' => 'Can edit existing roles',
        ]);

        $deleteRoles = Permission::create([
            'name' => 'delete-roles',
            'display_name' => 'Delete Roles',
            'description' => 'Can delete roles',
        ]);

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->permissions()->attach([
            $viewUsers->id,
            $createUsers->id,
            $editUsers->id,
            $deleteUsers->id,
            $viewRoles->id,
            $createRoles->id,
            $editRoles->id,
            $deleteRoles->id,
        ]);

        $managerRole = Role::where('name', 'manager')->first();
        $managerRole->permissions()->attach([
            $viewUsers->id,
            $createUsers->id,
            $editUsers->id,
            $viewRoles->id,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'status' => true,
        ]);

        // Get user role
        $userRole = Role::where('name', 'user')->first();

        // Create standard user
        User::create([
            'name' => 'Standard User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role_id' => $userRole->id,
            'status' => true,
        ]);

        // Get sales role
        $salesRole = Role::where('name', 'sales')->first();

        // Create sales user
        User::create([
            'name' => 'Sales Agent',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role_id' => $salesRole->id,
            'status' => true,
        ]);

        // Get manager role
        $managerRole = Role::where('name', 'manager')->first();

        // Create manager user
        User::create([
            'name' => 'Sales Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id,
            'status' => true,
        ]);
    }
}

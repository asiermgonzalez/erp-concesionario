<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin role
        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'System administrator with full access',
        ]);
        
        // Create user role
        Role::create([
            'name' => 'user',
            'display_name' => 'Standard User',
            'description' => 'Standard user with limited access',
        ]);
        
        // Create sales role
        Role::create([
            'name' => 'sales',
            'display_name' => 'Sales Agent',
            'description' => 'Sales agent with client and vehicle management',
        ]);
        
        // Create manager role
        Role::create([
            'name' => 'manager',
            'display_name' => 'Sales Manager',
            'description' => 'Sales manager with reports and approval access',
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'username' => 'admin',
                'password' => bcrypt('password123'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin',
                'is_active' => true,
                'phone' => '1234567890',
                'address' => '123 Admin St'
            ]
        );

        // Create test client user
        $client = User::firstOrCreate(
            ['email' => 'client@example.com'],
            [
                'username' => 'client',
                'password' => bcrypt('password123'),
                'first_name' => 'Client',
                'last_name' => 'User',
                'role' => 'client',
                'is_active' => true,
                'phone' => '9876543210',
                'address' => '456 Client Ave'
            ]
        );

        // Create test staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'username' => 'staff',
                'password' => bcrypt('password123'),
                'first_name' => 'Staff',
                'last_name' => 'User',
                'role' => 'staff',
                'is_active' => true,
                'phone' => '5555555555',
                'address' => '789 Staff Blvd'
            ]
        );

        $this->command->info('Test users created successfully');
        $this->command->line('Admin: admin@example.com / password123');
        $this->command->line('Client: client@example.com / password123');
        $this->command->line('Staff: staff@example.com / password123');
    }
}

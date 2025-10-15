<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🧍‍♂️ Super Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]
        );

        // 👨‍💼 Warehouse Manager
        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Warehouse Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
            ]
        );

        // 👀 Read-only Viewer
        User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Inventory Viewer',
                'email' => 'viewer@example.com',
                'password' => Hash::make('password'),
            ]
        );

        $this->command->info('✅ Users seeded successfully!');
        $this->command->warn('Login credentials:');
        $this->command->line('--------------------------------');
        $this->command->line('Admin:   admin@example.com / password');
        $this->command->line('Manager: manager@example.com / password');
        $this->command->line('Viewer:  viewer@example.com / password');
        $this->command->line('--------------------------------');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed test user accounts for development and testing.
     */
    public function run(): void
    {
        // In production, passwords MUST be set via env vars — never use defaults
        $defaultPassword = env('SEED_USER_PASSWORD');
        if (empty($defaultPassword)) {
            $this->command->warn('SEED_USER_PASSWORD not set. Generating secure random password for test users.');
            $defaultPassword = bin2hex(random_bytes(16));
        }

        $adminPassword = env('ADMIN_PASSWORD');
        if (empty($adminPassword)) {
            $this->command->warn('ADMIN_PASSWORD not set. Generating secure random password for admin.');
            $adminPassword = bin2hex(random_bytes(16));
        }

        // Permanent admin user
        User::firstOrCreate(
            ['email' => 'admin@autoscout.dev'],
            [
                'name' => 'Admin',
                'password' => $adminPassword,
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@autoscout24.com'],
            [
                'name' => 'Admin User',
                'password' => $defaultPassword,
                'phone' => '+49 123 456 7890',
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Test buyer
        User::firstOrCreate(
            ['email' => 'buyer@autoscout24.com'],
            [
                'name' => 'Test Buyer',
                'password' => $defaultPassword,
                'phone' => '+49 123 456 7891',
                'email_verified_at' => now(),
            ]
        );

        // Test seller
        User::firstOrCreate(
            ['email' => 'seller@autoscout24.com'],
            [
                'name' => 'Test Seller',
                'password' => $defaultPassword,
                'phone' => '+49 123 456 7892',
                'email_verified_at' => now(),
            ]
        );

        // Test dealer
        User::firstOrCreate(
            ['email' => 'dealer@autoscout24.com'],
            [
                'name' => 'Test Dealer',
                'password' => $defaultPassword,
                'phone' => '+49 123 456 7893',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test users seeded successfully.');
        $this->command->info('Set SEED_USER_PASSWORD in .env to control test user passwords.');
    }
}

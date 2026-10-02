<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@election.com'],
            [
                'name' => 'অ্যাডমিন ম্যানেজার',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Admin User Created/Updated Successfully!");
        $this->command->info("Email: admin@election.com");
        $this->command->info("Password: admin123");
    }
}

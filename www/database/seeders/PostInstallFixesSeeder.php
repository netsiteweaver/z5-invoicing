<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class PostInstallFixesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure users without explicit status are marked Active
        User::whereNull('status')->update(['status' => 1]);

        // Ensure default admin user is active and has admin role
        $adminUser = User::where('email', 'admin@z5distribution.com')->first();
        if ($adminUser) {
            $adminUser->update(['status' => 1]);
            $adminUser->assignRole('admin');
        }
    }
}



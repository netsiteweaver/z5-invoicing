<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Support\Str;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create a default one
        $adminUser = User::where('user_level', 'Root')->first();
        
        if (!$adminUser) {
            $adminUser = User::first();
        }
        
        if (!$adminUser) {
            // Create a default admin user if none exists
            $adminUser = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@z5distribution.com',
                'password' => bcrypt('password'),
                'user_level' => 'Root',
                'job_title' => 'System Administrator',
                'status' => 1,
            ]);
        }

        // Create default company settings
        CompanySetting::firstOrCreate(
            ['company_name' => 'Z5 Distribution System'],
            [
                'uuid' => Str::uuid(),
                'company_name' => 'Z5 Distribution System',
                'legal_name' => 'Z5 Distribution System Ltd',
                'brn' => 'C123456789',
                'vat_number' => 'VAT123456789',
                'address' => '123 Business Street, Port Louis',
                'city' => 'Port Louis',
                'postal_code' => '11300',
                'country' => 'Mauritius',
                'phone_primary' => '+230 123 4567',
                'phone_secondary' => '+230 123 4568',
                'email_primary' => 'info@z5distribution.com',
                'email_secondary' => 'support@z5distribution.com',
                'website' => 'https://z5distribution.com',
                'description' => 'Professional distribution management system for modern businesses.',
                'currency' => 'MUR',
                'timezone' => 'Indian/Mauritius',
                'date_format' => 'd/m/Y',
                'time_format' => 'H:i',
                'is_active' => true,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
            ]
        );
    }
}

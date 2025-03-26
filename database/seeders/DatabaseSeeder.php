<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);

        SystemSetting::create([
            'system_title'         => 'My System',
            'system_short_title'   => 'MS',
            'company_name'         => 'My Company',
            'tag_line'             => 'Your tagline here',
            'phone_code'           => '+1',
            'phone_number'         => '1234567890',
            'whatsapp'             => '1234567890',
            'email'                => 'info@example.com',
            'time_zone'            => 'UTC',
            'language'             => 'en',
            'admin_title'          => 'Admin Panel',
            'admin_short_title'    => 'AP',
            'copyright_text'       => '© 2025 My Company. All rights reserved.',
            'admin_copyright_text' => '© 2025 My Company. All rights reserved.',
        ]);
    }
}

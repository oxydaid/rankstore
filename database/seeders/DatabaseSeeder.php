<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\AppSetting::create([
            'site_name' => 'Rank Shop',
            'server_ip' => 'play.cubecraft.net',
            'server_port' => '19132',
            'primary_color' => '#d97706',
            'secondary_color' => '#581c87',
        ]);

        // Membuat user admin default
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
    }
}

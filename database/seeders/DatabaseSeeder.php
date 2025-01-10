<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ComicFormatSeeder::class,
            SettingsSeeder::class,
            TeamSeeder::class,
            UserSeeder::class,
            // TestSeeder::class, // Dev only
        ]);
    }
}

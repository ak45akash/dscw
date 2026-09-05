<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SettingsSeeder::class,
            AdminUserSeeder::class,
            ServiceSeeder::class,
            BlogPostSeeder::class,
            PageSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
            GallerySeeder::class,
            LocationSeeder::class,
        ]);
    }
}

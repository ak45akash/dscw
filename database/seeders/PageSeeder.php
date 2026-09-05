<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = require __DIR__.'/content/Pages.php';

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'template' => $page['template'] ?? 'default',
                    'meta_title' => $page['meta_title'],
                    'meta_description' => $page['meta_description'],
                    'is_active' => true,
                ]
            );
        }
    }
}

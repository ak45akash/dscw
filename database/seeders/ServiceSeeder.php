<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'exterior' => ServiceCategory::query()->updateOrCreate(
                ['slug' => 'exterior'],
                ['name' => 'Exterior Care', 'description' => 'Professional exterior washing, steam cleaning, and paint-safe treatments that restore shine without damaging your finish.', 'display_order' => 1]
            ),
            'interior' => ServiceCategory::query()->updateOrCreate(
                ['slug' => 'interior'],
                ['name' => 'Interior Care', 'description' => 'Deep interior cleaning, sanitization, and conditioning for a fresh cabin experience.', 'display_order' => 2]
            ),
            'protection' => ServiceCategory::query()->updateOrCreate(
                ['slug' => 'protection'],
                ['name' => 'Paint Protection', 'description' => 'Long-term paint protection through ceramic coating, PPF, and advanced sealants.', 'display_order' => 3]
            ),
            'detailing' => ServiceCategory::query()->updateOrCreate(
                ['slug' => 'detailing'],
                ['name' => 'Detailing', 'description' => 'Comprehensive detailing packages for showroom-level presentation inside and out.', 'display_order' => 4]
            ),
        ];

        $services = $this->servicesData();

        foreach ($services as $index => $data) {
            $category = $categories[$data['category']] ?? $categories['exterior'];

            Service::query()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'service_category_id' => $category->id,
                    'name' => $data['name'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'duration_minutes' => $data['duration_minutes'],
                    'is_featured' => $data['is_featured'] ?? false,
                    'featured_order' => $data['featured_order'] ?? null,
                    'is_active' => true,
                    'display_order' => $index + 1,
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                ]
            );
        }
    }

    private function servicesData(): array
    {
        return require __DIR__.'/content/Services.php';
    }
}

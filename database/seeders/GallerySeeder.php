<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['title' => 'Sedan Paint Revival', 'service' => 'Paint Correction', 'category' => 'Exterior', 'description' => 'Removed years of swirl marks and oxidation from a daily-driven sedan, restoring deep gloss and clarity.', 'is_featured' => true],
            ['title' => 'SUV Interior Transformation', 'service' => 'Interior Deep Clean', 'category' => 'Interior', 'description' => 'Complete interior reset including steam sanitization, leather conditioning, and odour elimination.', 'is_featured' => true],
            ['title' => 'Sports Car Ceramic Finish', 'service' => 'Ceramic Coating', 'category' => 'Protection', 'description' => 'Multi-layer ceramic coating application with paint correction prep for a mirror-like finish.', 'is_featured' => true],
            ['title' => 'Headlight Clarity Restore', 'service' => 'Headlight Restoration', 'category' => 'Exterior', 'description' => 'Yellowed, hazy headlights restored to crystal clarity with UV sealant protection.', 'is_featured' => true],
            ['title' => 'Hatchback Steam Detail', 'service' => 'Premium Steam Wash', 'category' => 'Exterior', 'description' => 'Full exterior steam wash with engine bay cleaning and tyre dressing.', 'is_featured' => true],
            ['title' => 'Luxury Sedan Full Detail', 'service' => 'Full Detailing', 'category' => 'Detailing', 'description' => 'Showroom-level detailing inside and out for a pre-event presentation.', 'is_featured' => false],
            ['title' => 'Alloy Wheel Refinement', 'service' => 'Alloy Wheel Care', 'category' => 'Exterior', 'description' => 'Brake dust removal, barrel cleaning, and protective wheel sealant application.', 'is_featured' => false],
            ['title' => 'PPF Bonnet Protection', 'service' => 'PPF', 'category' => 'Protection', 'description' => 'Self-healing paint protection film applied to bonnet and front bumper.', 'is_featured' => false],
        ];

        foreach ($items as $i => $item) {
            GalleryItem::query()->updateOrCreate(
                ['title' => $item['title']],
                [
                    'description' => $item['description'],
                    'service' => $item['service'],
                    'category' => $item['category'],
                    'is_featured' => $item['is_featured'],
                    'display_order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}

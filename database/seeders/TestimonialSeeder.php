<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Rahul Mehta', 'service' => 'Premium Steam Wash', 'rating' => 5, 'review' => 'Absolutely impressed with the steam wash. My BMW looks brand new and the attention to detail on the wheels and door jambs was exceptional. Will definitely be a regular customer.', 'is_featured' => true],
            ['name' => 'Priya Sharma', 'service' => 'Interior Deep Clean', 'rating' => 5, 'review' => 'Had kids and pets in the car — the interior was in rough shape. They removed every stain and the cabin smells fresh. Professional team and transparent pricing.', 'is_featured' => true],
            ['name' => 'Arjun Patel', 'service' => 'Ceramic Coating', 'rating' => 5, 'review' => 'Got ceramic coating done on my new SUV. The team explained the entire process, showed me the hydrophobic effect, and the finish is incredible. Worth every rupee.', 'is_featured' => true],
            ['name' => 'Sneha Desai', 'service' => 'Full Detailing', 'rating' => 5, 'review' => 'Booked full detailing before selling my car. The transformation was unbelievable — got compliments from the buyer on how well maintained it looked. Highly recommend.', 'is_featured' => true],
            ['name' => 'Vikram Singh', 'service' => 'PPF Installation', 'rating' => 5, 'review' => 'PPF on the front bumper and bonnet saved my paint within the first month of driving in Mumbai traffic. Invisible protection and flawless installation.', 'is_featured' => true],
            ['name' => 'Ananya Reddy', 'service' => 'Basic Wash', 'rating' => 4, 'review' => 'Quick, affordable, and thorough basic wash. Perfect for my weekly maintenance routine. Staff is friendly and the waiting area is comfortable.'],
            ['name' => 'Karan Malhotra', 'service' => 'Paint Correction', 'rating' => 5, 'review' => 'Swirl marks and light scratches completely removed. The paint depth and clarity after correction was night and day. True professionals who care about results.'],
            ['name' => 'Meera Joshi', 'service' => 'Monsoon Protection', 'rating' => 5, 'review' => 'Monsoon package was exactly what my car needed. Underbody treatment, interior protection, and anti-fog treatment — all done properly. Feeling confident this rainy season.'],
        ];

        foreach ($testimonials as $i => $t) {
            Testimonial::query()->updateOrCreate(
                ['name' => $t['name'], 'service' => $t['service']],
                [
                    'review' => $t['review'],
                    'rating' => $t['rating'],
                    'is_featured' => $t['is_featured'] ?? false,
                    'display_order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}

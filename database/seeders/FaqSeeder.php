<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['category' => 'Booking', 'question' => 'How do I book a service online?', 'answer' => 'Visit our Book Now page, choose your preferred service, select a date and time slot, enter your vehicle details, and confirm your booking. You will receive an email confirmation once your appointment is scheduled. Same-day bookings may be available depending on slot availability.'],
            ['category' => 'Booking', 'question' => 'Can I reschedule or cancel my appointment?', 'answer' => 'Yes. Contact us at least 4 hours before your scheduled time to reschedule or cancel without penalty. Cancellations made with shorter notice may be subject to our cancellation policy. We understand plans change and will do our best to accommodate you.'],
            ['category' => 'Services', 'question' => 'What is the difference between a basic wash and premium steam wash?', 'answer' => 'A basic wash focuses on exterior cleaning with safe shampoos and hand drying. Our premium steam wash uses high-temperature steam to lift dirt from paint and crevices with minimal water, followed by paint-safe drying, tyre dressing, and interior vacuuming. Steam is gentler on paint and more effective for deep cleaning.'],
            ['category' => 'Services', 'question' => 'How long does ceramic coating last?', 'answer' => 'Professional ceramic coating typically lasts 2–5 years depending on the product grade, application quality, and maintenance. We use premium coatings and provide aftercare guidance. Regular maintenance washes and avoiding harsh chemicals help maximize coating lifespan.'],
            ['category' => 'Services', 'question' => 'Is paint protection film (PPF) worth it?', 'answer' => 'PPF is highly recommended for new vehicles and daily drivers in urban environments. It protects against stone chips, scratches, and road debris on high-impact areas like the bonnet, bumper, and mirrors. Combined with ceramic coating, PPF offers comprehensive paint protection.'],
            ['category' => 'Pricing', 'question' => 'Are your prices fixed or do they vary by vehicle size?', 'answer' => 'Our listed prices are starting prices for standard sedans and hatchbacks. SUVs, luxury vehicles, and heavily soiled cars may require additional time and products. We provide a clear quote before starting any service so there are no surprises.'],
            ['category' => 'Pricing', 'question' => 'Do you offer package deals or memberships?', 'answer' => 'We offer bundled packages such as our Monsoon Protection Package and Full Detailing bundle at discounted rates compared to individual services. Membership and loyalty programs are planned for the future — contact us for current promotional offers.'],
            ['category' => 'Process', 'question' => 'How long will my car be at the facility?', 'answer' => 'Service duration varies: a basic wash takes 30–45 minutes, premium steam wash about 75 minutes, interior deep clean 2–3 hours, and full detailing or coating services may require 4–8 hours or a full day. We provide estimated completion times when you book.'],
            ['category' => 'Process', 'question' => 'Do I need to stay while my car is being serviced?', 'answer' => 'No, you can drop off your vehicle and return at the agreed time. For longer services like ceramic coating or PPF, we recommend scheduling a convenient drop-off. Our team will contact you when your vehicle is ready for collection.'],
            ['category' => 'Safety', 'question' => 'Is steam washing safe for all paint types?', 'answer' => 'Yes. Steam washing is safe for factory paint, clear coat, matte finishes (with appropriate products), and wrapped vehicles when performed by trained technicians. We adjust temperature and technique based on your vehicle\'s finish and condition.'],
            ['category' => 'Safety', 'question' => 'What products do you use on interiors?', 'answer' => 'We use pH-neutral, automotive-grade cleaners for fabric, leather, plastic, and glass. All products are selected for effectiveness without damaging surfaces. Leather services include conditioning to prevent cracking and fading.'],
            ['category' => 'Location', 'question' => 'Where are you located?', 'answer' => 'Diamond Steam Car Wash is located in Mumbai, Maharashtra. Full address and directions are available on our Contact page. We serve customers across the Mumbai metropolitan area and welcome vehicles of all makes and models.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::query()->updateOrCreate(
                ['question' => $faq['question']],
                [
                    'answer' => $faq['answer'],
                    'category' => $faq['category'],
                    'display_order' => $i + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}

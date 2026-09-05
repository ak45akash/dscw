<?php

namespace Database\Seeders;

use App\Models\BlockedDate;
use App\Models\Location;
use App\Models\LocationWorkingHour;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Diamond Steam — Andheri',
                'slug' => 'andheri',
                'phone' => '+91 98765 43210',
                'email' => 'andheri@diamondsteamcarwash.com',
                'address' => '12 Premium Auto Lane, Near Metro Station',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400053',
                'is_default' => true,
                'display_order' => 1,
                'hours' => [
                    // 0=Sun … 6=Sat
                    0 => ['10:00', '18:00'],
                    1 => ['09:00', '20:00'],
                    2 => ['09:00', '20:00'],
                    3 => ['09:00', '20:00'],
                    4 => ['09:00', '20:00'],
                    5 => ['09:00', '20:00'],
                    6 => ['09:00', '19:00'],
                ],
            ],
            [
                'name' => 'Diamond Steam — Navi Mumbai',
                'slug' => 'navi-mumbai',
                'phone' => '+91 98765 43211',
                'email' => 'navimumbai@diamondsteamcarwash.com',
                'address' => 'Plot 45, Sector 17, Palm Beach Road',
                'city' => 'Navi Mumbai',
                'state' => 'Maharashtra',
                'pincode' => '400703',
                'is_default' => false,
                'display_order' => 2,
                'hours' => [
                    0 => null, // closed
                    1 => ['09:00', '19:00'],
                    2 => ['09:00', '19:00'],
                    3 => ['09:00', '19:00'],
                    4 => ['09:00', '19:00'],
                    5 => ['09:00', '19:00'],
                    6 => ['09:00', '18:00'],
                ],
            ],
        ];

        foreach ($locations as $data) {
            $hours = $data['hours'];
            unset($data['hours']);

            $location = Location::query()->updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true])
            );

            foreach (range(0, 6) as $day) {
                $slot = $hours[$day] ?? null;
                LocationWorkingHour::query()->updateOrCreate(
                    ['location_id' => $location->id, 'day_of_week' => $day],
                    [
                        'opens_at' => $slot[0] ?? null,
                        'closes_at' => $slot[1] ?? null,
                        'is_closed' => $slot === null,
                    ]
                );
            }
        }

        $andheri = Location::query()->where('slug', 'andheri')->first();

        if ($andheri) {
            BlockedDate::query()->updateOrCreate(
                [
                    'location_id' => $andheri->id,
                    'date' => now()->addDays(14)->toDateString(),
                ],
                [
                    'reason' => 'Staff training day',
                    'is_full_day' => true,
                ]
            );
        }
    }
}

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
        // Rename legacy Mumbai slugs so bookings keep their foreign keys.
        Location::query()->where('slug', 'andheri')->update(['slug' => 'sector-66']);
        Location::query()->where('slug', 'navi-mumbai')->update(['slug' => 'matour']);

        $locations = [
            [
                'name' => 'Diamond Steam — Sector 66',
                'slug' => 'sector-66',
                'phone' => '+91 98765 43210',
                'email' => 'sector66@diamondsteamcarwash.com',
                'address' => 'Plot Number 589, Sector 66, Near Bestech Mall And Business Towers',
                'city' => 'Sahibzada Ajit Singh Nagar',
                'state' => 'Punjab',
                'pincode' => '160062',
                'is_default' => true,
                'display_order' => 1,
                'hours' => [
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
                'name' => 'Diamond Steam — Matour',
                'slug' => 'matour',
                'phone' => '+91 98765 43211',
                'email' => 'matour@diamondsteamcarwash.com',
                'address' => '',
                'city' => 'Matour',
                'state' => 'Punjab',
                'pincode' => '',
                'is_default' => false,
                'display_order' => 2,
                'hours' => [
                    0 => null,
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

        $primary = Location::query()->where('slug', 'sector-66')->first();

        if ($primary) {
            BlockedDate::query()->updateOrCreate(
                [
                    'location_id' => $primary->id,
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

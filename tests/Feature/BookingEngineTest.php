<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_availability_returns_slots_for_open_day(): void
    {
        $location = Location::query()->where('slug', 'sector-66')->firstOrFail();
        $service = Service::query()->active()->firstOrFail();

        // Next Monday
        $date = now()->next('Monday')->startOfDay();
        if ($date->isPast()) {
            $date->addWeek();
        }

        $slots = app(AvailabilityService::class)->slotsFor($location, $service, $date);

        $this->assertNotEmpty($slots);
        $this->assertArrayHasKey('start', $slots[0]);
        $this->assertArrayHasKey('label', $slots[0]);
    }

    public function test_slots_endpoint_returns_json(): void
    {
        $location = Location::query()->where('slug', 'sector-66')->firstOrFail();
        $service = Service::query()->active()->firstOrFail();
        $date = now()->next('Monday')->toDateString();

        $this->getJson(route('booking.slots', [
            'location_id' => $location->id,
            'service_id' => $service->id,
            'date' => $date,
        ]))
            ->assertOk()
            ->assertJsonStructure(['slots']);
    }

    public function test_guest_can_create_pay_at_location_booking(): void
    {
        $location = Location::query()->where('slug', 'sector-66')->firstOrFail();
        $service = Service::query()->active()->orderBy('price')->firstOrFail();
        $date = now()->next('Tuesday')->startOfDay();

        $slots = app(AvailabilityService::class)->slotsFor($location, $service, $date);
        $this->assertNotEmpty($slots);

        $response = $this->postJson(route('booking.store'), [
            'location_id' => $location->id,
            'service_id' => $service->id,
            'customer_name' => 'Test Driver',
            'customer_email' => 'driver@example.com',
            'customer_phone' => '+919999999999',
            'vehicle_make_model' => 'Honda City',
            'booking_date' => $date->toDateString(),
            'start_time' => $slots[0]['start'],
            'payment_method' => 'at_location',
        ]);

        $response->assertOk()->assertJsonStructure(['reference', 'confirmation_url']);

        $this->assertDatabaseHas('bookings', [
            'customer_email' => 'driver@example.com',
            'payment_method' => 'at_location',
            'status' => Booking::STATUS_CONFIRMED,
        ]);
    }

    public function test_admin_bookings_index_requires_auth(): void
    {
        $this->get(route('admin.bookings.index'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_bookings(): void
    {
        $admin = User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.bookings.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.services.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.locations.index'))
            ->assertOk();
    }

    public function test_book_now_page_loads(): void
    {
        $this->get(route('booking.index'))
            ->assertOk()
            ->assertSee('Choose a location');
    }
}

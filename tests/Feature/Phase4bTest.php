<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Location;
use App\Models\Media;
use App\Models\Service;
use App\Models\ServiceAddon;
use App\Models\User;
use App\Services\AvailabilityService;
use App\Services\SettingsService;
use App\Services\SmsService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase4bTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::query()->where('email', 'admin@diamondsteamcarwash.com')->firstOrFail();
    }

    public function test_admin_can_manage_service_addons(): void
    {
        $admin = $this->admin();
        $service = Service::query()->active()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.service-addons.store'), [
                'name' => 'Engine Bay Dressing',
                'description' => 'Protective finish',
                'price' => 499,
                'duration_minutes' => 30,
                'display_order' => 1,
                'is_active' => 1,
                'service_ids' => [$service->id],
            ])
            ->assertRedirect(route('admin.service-addons.index'));

        $addon = ServiceAddon::query()->where('slug', 'engine-bay-dressing')->firstOrFail();
        $this->assertTrue($addon->services->contains('id', $service->id));

        $this->actingAs($admin)->get(route('admin.service-addons.index'))->assertOk()->assertSee('Engine Bay Dressing');
    }

    public function test_booking_with_addon_increases_price_and_duration(): void
    {
        Mail::fake();

        $location = Location::query()->where('slug', 'sector-66')->firstOrFail();
        $service = Service::query()->active()->orderBy('price')->firstOrFail();
        $addon = ServiceAddon::query()->create([
            'name' => 'Pet Hair Removal',
            'slug' => 'pet-hair-removal',
            'price' => 300,
            'duration_minutes' => 30,
            'is_active' => true,
            'display_order' => 1,
        ]);
        $addon->services()->attach($service->id);

        $date = now()->next('Wednesday')->startOfDay();
        $slots = app(AvailabilityService::class)->slotsFor($location, $service, $date, 30);
        $this->assertNotEmpty($slots);

        $expected = round((float) $service->price + 300, 2);

        $this->postJson(route('booking.store'), [
            'location_id' => $location->id,
            'service_id' => $service->id,
            'addon_ids' => [$addon->id],
            'customer_name' => 'Addon User',
            'customer_email' => 'addon@example.com',
            'customer_phone' => '+919333333333',
            'vehicle_make_model' => 'Creta',
            'booking_date' => $date->toDateString(),
            'start_time' => $slots[0]['start'],
            'payment_method' => 'at_location',
        ])->assertOk();

        $booking = Booking::query()->where('customer_email', 'addon@example.com')->firstOrFail();
        $this->assertEquals($expected, (float) $booking->price);
        $this->assertSame((int) $service->duration_minutes + 30, (int) $booking->duration_minutes);
        $this->assertCount(1, $booking->addons);
        $this->assertSame('Pet Hair Removal', $booking->addons->first()->name);
    }

    public function test_admin_can_upload_media(): void
    {
        Storage::fake(config('dscw.media_disk', 'public'));
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->image('wash.jpg', 800, 600),
                'alt' => 'Wash bay',
                'folder' => 'media',
            ])
            ->assertRedirect(route('admin.media.index'));

        $this->assertDatabaseHas('media', ['alt' => 'Wash bay', 'folder' => 'media']);
        $media = Media::query()->firstOrFail();
        Storage::disk($media->disk)->assertExists($media->path);

        $this->actingAs($admin)->get(route('admin.media.index'))->assertOk()->assertSee('Wash bay', false);
    }

    public function test_sms_reminder_command_sends_when_configured(): void
    {
        Http::fake([
            'api.msg91.com/*' => Http::response(['type' => 'success'], 200),
        ]);

        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);
        $settings->setMany('sms', [
            'enabled' => ['value' => true, 'type' => 'boolean'],
            'provider' => 'msg91',
            'api_key' => 'test-key',
            'sender_id' => 'DSCW',
            'reminders_enabled' => ['value' => true, 'type' => 'boolean'],
            'reminder_hours_before' => ['value' => 24, 'type' => 'integer'],
            'reminder_template' => 'Reminder {reference}',
        ]);

        $location = Location::query()->firstOrFail();
        $service = Service::query()->active()->firstOrFail();
        $starts = now()->addHours(12);

        $booking = Booking::query()->create([
            'reference' => 'SMS-TEST-1',
            'location_id' => $location->id,
            'service_id' => $service->id,
            'customer_name' => 'SMS Customer',
            'customer_email' => 'sms@example.com',
            'customer_phone' => '9876543210',
            'vehicle_make_model' => 'City',
            'booking_date' => $starts->toDateString(),
            'start_time' => $starts->format('H:i:s'),
            'end_time' => $starts->copy()->addHour()->format('H:i:s'),
            'duration_minutes' => 60,
            'price' => 1000,
            'status' => Booking::STATUS_CONFIRMED,
            'payment_method' => Booking::PAYMENT_AT_LOCATION,
            'payment_status' => Booking::PAYMENT_UNPAID,
            'confirmed_at' => now(),
        ]);

        $this->artisan('bookings:send-sms-reminders')->assertSuccessful();

        $this->assertNotNull($booking->fresh()->sms_reminder_sent_at);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'msg91.com'));
    }

    public function test_dashboard_shows_phase_4_complete(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Phase 4 Complete', false)
            ->assertSee('Add-ons, media library, SMS reminders', false);
    }

    public function test_sms_service_skips_when_disabled(): void
    {
        $this->assertFalse(app(SmsService::class)->gateway()->isConfigured());
    }
}

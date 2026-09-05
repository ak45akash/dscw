<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmationMail;
use App\Mail\ContactEnquiryMail;
use App\Mail\NewBookingAdminMail;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Location;
use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase3CmsTest extends TestCase
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

    public function test_admin_can_manage_faqs(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.faqs.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.faqs.store'), [
                'question' => 'How long does a wash take?',
                'answer' => 'Typically 45–90 minutes depending on the service.',
                'category' => 'Services',
                'display_order' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $faq = Faq::query()->where('question', 'How long does a wash take?')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.faqs.update', $faq), [
                'question' => 'How long does a wash take?',
                'answer' => 'Usually under two hours.',
                'category' => 'Services',
                'display_order' => 2,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.faqs.index'));

        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'display_order' => 2,
            'answer' => 'Usually under two hours.',
        ]);
    }

    public function test_admin_can_create_page(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.pages.store'), [
                'title' => 'Care Tips',
                'content' => '<p>Keep your car clean.</p>',
                'meta_title' => 'Care Tips',
                'meta_description' => 'Tips',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.pages.index'));

        $this->assertDatabaseHas('pages', ['title' => 'Care Tips']);
    }

    public function test_admin_calendar_and_cms_indexes_load(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.bookings.calendar'))
            ->assertOk()
            ->assertSee('booking-cal__grid', false)
            ->assertSee('booking-cal__weekday', false);
        $this->actingAs($admin)->get(route('admin.blog-posts.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.blog-taxonomies.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.gallery-items.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.testimonials.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.enquiries.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.coupons.index'))->assertOk();
    }

    public function test_contact_form_stores_enquiry_and_emails_admin(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), [
            'name' => 'Priya Shah',
            'email' => 'priya@example.com',
            'phone' => '+919876543210',
            'subject' => 'Quote request',
            'message' => 'Looking for ceramic coating pricing.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_enquiries', [
            'email' => 'priya@example.com',
            'subject' => 'Quote request',
        ]);

        Mail::assertSent(ContactEnquiryMail::class);
    }

    public function test_booking_sends_customer_and_admin_emails(): void
    {
        Mail::fake();

        $location = Location::query()->where('slug', 'andheri')->firstOrFail();
        $service = Service::query()->active()->orderBy('price')->firstOrFail();
        $date = now()->next('Wednesday')->startOfDay();
        $slots = app(AvailabilityService::class)->slotsFor($location, $service, $date);
        $this->assertNotEmpty($slots);

        $this->postJson(route('booking.store'), [
            'location_id' => $location->id,
            'service_id' => $service->id,
            'customer_name' => 'Mail Test',
            'customer_email' => 'mailtest@example.com',
            'customer_phone' => '+919111111111',
            'vehicle_make_model' => 'Swift',
            'booking_date' => $date->toDateString(),
            'start_time' => $slots[0]['start'],
            'payment_method' => 'at_location',
        ])->assertOk();

        Mail::assertSent(BookingConfirmationMail::class);
        Mail::assertSent(NewBookingAdminMail::class);
    }

    public function test_coupon_applies_discount_to_booking_price(): void
    {
        Mail::fake();

        $coupon = Coupon::query()->create([
            'code' => 'SAVE20',
            'type' => Coupon::TYPE_PERCENT,
            'value' => 20,
            'is_active' => true,
        ]);

        $location = Location::query()->where('slug', 'andheri')->firstOrFail();
        $service = Service::query()->active()->orderByDesc('price')->firstOrFail();
        $date = now()->next('Thursday')->startOfDay();
        $slots = app(AvailabilityService::class)->slotsFor($location, $service, $date);
        $this->assertNotEmpty($slots);

        $expected = round((float) $service->price * 0.8, 2);

        $this->postJson(route('booking.store'), [
            'location_id' => $location->id,
            'service_id' => $service->id,
            'customer_name' => 'Coupon User',
            'customer_email' => 'coupon@example.com',
            'customer_phone' => '+919222222222',
            'vehicle_make_model' => 'City',
            'booking_date' => $date->toDateString(),
            'start_time' => $slots[0]['start'],
            'payment_method' => 'at_location',
            'coupon_code' => 'save20',
        ])->assertOk();

        $booking = Booking::query()->where('customer_email', 'coupon@example.com')->firstOrFail();
        $this->assertEquals($expected, (float) $booking->price);
        $this->assertEquals('SAVE20', $booking->coupon_code);
        $this->assertSame(1, $coupon->fresh()->used_count);
    }

    public function test_invalid_coupon_is_rejected(): void
    {
        $this->postJson(route('booking.coupon'), [
            'code' => 'NOPE',
            'service_id' => Service::query()->active()->firstOrFail()->id,
        ])->assertStatus(422);

        $admin = $this->admin();
        $this->actingAs($admin)
            ->post(route('admin.coupons.store'), [
                'code' => 'FLAT100',
                'type' => 'fixed',
                'value' => 100,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', ['code' => 'FLAT100']);
    }

    public function test_dashboard_shows_phase_3_complete(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Phase 3 Complete', false)
            ->assertSee('Phase 4 Complete', false);
    }
}

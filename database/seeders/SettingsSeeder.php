<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var SettingsService $settings */
        $settings = app(SettingsService::class);

        $settings->setMany('business', [
            'business_name' => config('dscw.business.name'),
            'tagline' => config('dscw.business.tagline'),
            'phone' => '+91 98765 43210',
            'email' => 'hello@diamondsteamcarwash.com',
            'address' => '123 Premium Auto Lane, Mumbai, Maharashtra 400001',
            'currency' => 'INR',
            'timezone' => 'Asia/Kolkata',
            'facebook_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'whatsapp_number' => '+919876543210',
        ], [
            'business_name', 'tagline', 'phone', 'email', 'address',
            'currency', 'timezone', 'facebook_url', 'instagram_url',
            'youtube_url', 'whatsapp_number',
        ]);

        $settings->set('theme', 'mode', 'system', 'string', true);

        $settings->setMany('seo', [
            'meta_title' => 'Diamond Steam Car Wash | Premium Car Care & Detailing',
            'meta_description' => 'Book premium steam car wash, detailing, ceramic coating, and paint protection services. Fast online booking with expert care.',
        ], ['meta_title', 'meta_description']);

        $settings->setMany('booking', [
            'online_bookings_enabled' => ['value' => true, 'type' => 'boolean'],
            'same_day_bookings' => ['value' => true, 'type' => 'boolean'],
            'max_advance_days' => ['value' => 30, 'type' => 'integer'],
            'slot_interval_minutes' => ['value' => 60, 'type' => 'integer'],
            'default_duration_minutes' => ['value' => 60, 'type' => 'integer'],
            'buffer_minutes' => ['value' => 15, 'type' => 'integer'],
            'max_bookings_per_day' => ['value' => 20, 'type' => 'integer'],
            'max_bookings_per_slot' => ['value' => 2, 'type' => 'integer'],
            'require_admin_approval' => ['value' => false, 'type' => 'boolean'],
            'auto_confirm_bookings' => ['value' => true, 'type' => 'boolean'],
        ]);

        $settings->setMany('payment', [
            'razorpay_enabled' => ['value' => false, 'type' => 'boolean'],
            'razorpay_key_id' => ['value' => '', 'type' => 'string', 'is_public' => true],
        ], ['razorpay_key_id']);

        $settings->setMany('email', [
            'mail_from_name' => config('dscw.business.name'),
            'mail_from_address' => 'hello@diamondsteamcarwash.com',
        ]);

        $settings->setMany('analytics', [
            'ga_measurement_id' => '',
            'gtm_container_id' => '',
        ]);

        $settings->setMany('sms', [
            'enabled' => ['value' => false, 'type' => 'boolean'],
            'provider' => 'null',
            'api_key' => '',
            'account_sid' => '',
            'sender_id' => 'DSCW',
            'from_number' => '',
            'template_id' => '',
            'confirmations_enabled' => ['value' => false, 'type' => 'boolean'],
            'reminders_enabled' => ['value' => false, 'type' => 'boolean'],
            'reminder_hours_before' => ['value' => 24, 'type' => 'integer'],
            'confirmation_template' => 'Hi {name}, your DSCW booking {reference} is confirmed for {date} at {time}.',
            'reminder_template' => 'Reminder: {name}, DSCW appointment {reference} on {date} at {time}.',
        ]);
    }
}

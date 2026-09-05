<?php

namespace App\Services;

use App\Contracts\SmsGateway;
use App\Models\Booking;
use App\Services\Sms\Msg91SmsGateway;
use App\Services\Sms\NullSmsGateway;
use App\Services\Sms\TwilioSmsGateway;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Throwable;

class SmsService
{
    public function __construct(private SettingsService $settings) {}

    public function gateway(): SmsGateway
    {
        if (! $this->settings->get('sms', 'enabled', false)) {
            return new NullSmsGateway;
        }

        return match ((string) $this->settings->get('sms', 'provider', 'null')) {
            'msg91' => app(Msg91SmsGateway::class),
            'twilio' => app(TwilioSmsGateway::class),
            default => new NullSmsGateway,
        };
    }

    public function sendConfirmation(Booking $booking): bool
    {
        if (! $this->settings->get('sms', 'confirmations_enabled', false)) {
            return false;
        }

        $gateway = $this->gateway();
        if (! $gateway->isConfigured()) {
            return false;
        }

        $template = (string) $this->settings->get(
            'sms',
            'confirmation_template',
            'Hi {name}, your DSCW booking {reference} is confirmed for {date} at {time}. See you soon!'
        );

        try {
            $gateway->send($booking->customer_phone, $this->render($template, $booking));
            $booking->update(['sms_confirmation_sent_at' => now()]);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function sendReminder(Booking $booking): bool
    {
        $gateway = $this->gateway();
        if (! $gateway->isConfigured()) {
            return false;
        }

        $template = (string) $this->settings->get(
            'sms',
            'reminder_template',
            'Reminder: {name}, your DSCW appointment {reference} is tomorrow ({date} {time}). Reply if you need to reschedule.'
        );

        try {
            $gateway->send($booking->customer_phone, $this->render($template, $booking));
            $booking->update(['sms_reminder_sent_at' => now()]);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return Collection<int, Booking>
     */
    public function dueReminders(): Collection
    {
        if (! $this->settings->get('sms', 'enabled', false)
            || ! $this->settings->get('sms', 'reminders_enabled', false)) {
            return collect();
        }

        $hours = max(1, (int) $this->settings->get('sms', 'reminder_hours_before', 24));
        $windowStart = now();
        $windowEnd = now()->addHours($hours);

        return Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereNull('sms_reminder_sent_at')
            ->whereNotNull('customer_phone')
            ->get()
            ->filter(function (Booking $booking) use ($windowStart, $windowEnd) {
                $startsAt = Carbon::parse(
                    $booking->booking_date->toDateString().' '.substr((string) $booking->start_time, 0, 8)
                );

                return $startsAt->gte($windowStart) && $startsAt->lte($windowEnd);
            })
            ->values();
    }

    private function render(string $template, Booking $booking): string
    {
        $time = substr((string) $booking->start_time, 0, 5);

        return strtr($template, [
            '{name}' => $booking->customer_name,
            '{reference}' => $booking->reference,
            '{date}' => $booking->booking_date?->format('d M Y') ?? '',
            '{time}' => $time,
            '{service}' => $booking->service?->name ?? '',
            '{location}' => $booking->location?->name ?? '',
        ]);
    }
}

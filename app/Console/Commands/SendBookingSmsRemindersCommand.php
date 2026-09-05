<?php

namespace App\Console\Commands;

use App\Services\SmsService;
use Illuminate\Console\Command;

class SendBookingSmsRemindersCommand extends Command
{
    protected $signature = 'bookings:send-sms-reminders';

    protected $description = 'Send SMS reminders for upcoming confirmed bookings';

    public function handle(SmsService $sms): int
    {
        $due = $sms->dueReminders();
        $sent = 0;

        foreach ($due as $booking) {
            if ($sms->sendReminder($booking->loadMissing(['service', 'location']))) {
                $sent++;
            }
        }

        $this->info("SMS reminders sent: {$sent} of {$due->count()} due.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TwilioSmsGateway implements SmsGateway
{
    public function __construct(private SettingsService $settings) {}

    public function send(string $to, string $body): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Twilio is not configured.');
        }

        $sid = (string) $this->settings->get('sms', 'account_sid', '');
        $token = (string) $this->settings->get('sms', 'api_key', '');
        $from = (string) $this->settings->get('sms', 'from_number', '');

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $this->e164($to),
                'Body' => $body,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Twilio SMS failed: '.$response->body());
        }
    }

    public function isConfigured(): bool
    {
        return filled($this->settings->get('sms', 'account_sid'))
            && filled($this->settings->get('sms', 'api_key'))
            && filled($this->settings->get('sms', 'from_number'));
    }

    private function e164(string $to): string
    {
        $digits = preg_replace('/\D+/', '', $to) ?? '';

        if (str_starts_with($to, '+')) {
            return '+'.$digits;
        }

        if (strlen($digits) === 10) {
            return '+91'.$digits;
        }

        return '+'.$digits;
    }
}

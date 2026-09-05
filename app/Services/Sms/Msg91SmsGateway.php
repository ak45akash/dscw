<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class Msg91SmsGateway implements SmsGateway
{
    public function __construct(private SettingsService $settings) {}

    public function send(string $to, string $body): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('MSG91 is not configured.');
        }

        $authKey = (string) $this->settings->get('sms', 'api_key', '');
        $sender = (string) $this->settings->get('sms', 'sender_id', 'DSCW');
        $templateId = (string) $this->settings->get('sms', 'template_id', '');

        $payload = [
            'sender' => $sender,
            'route' => '4',
            'country' => '91',
            'sms' => [[
                'message' => $body,
                'to' => [$this->normalizeIndianMobile($to)],
            ]],
        ];

        if ($templateId !== '') {
            $payload['template_id'] = $templateId;
        }

        $response = Http::withHeaders([
            'authkey' => $authKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.msg91.com/api/v2/sendsms', $payload);

        if (! $response->successful()) {
            throw new RuntimeException('MSG91 SMS failed: '.$response->body());
        }
    }

    public function isConfigured(): bool
    {
        return filled($this->settings->get('sms', 'api_key'));
    }

    private function normalizeIndianMobile(string $to): string
    {
        $digits = preg_replace('/\D+/', '', $to) ?? '';

        if (strlen($digits) === 10) {
            return '91'.$digits;
        }

        return $digits;
    }
}

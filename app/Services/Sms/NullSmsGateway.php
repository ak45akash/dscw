<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use RuntimeException;

class NullSmsGateway implements SmsGateway
{
    public function send(string $to, string $body): void
    {
        throw new RuntimeException('SMS provider is not configured.');
    }

    public function isConfigured(): bool
    {
        return false;
    }
}

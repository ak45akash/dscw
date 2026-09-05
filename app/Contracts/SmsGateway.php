<?php

namespace App\Contracts;

interface SmsGateway
{
    public function send(string $to, string $body): void;

    public function isConfigured(): bool;
}

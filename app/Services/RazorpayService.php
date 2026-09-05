<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RazorpayService
{
    public function __construct(private SettingsService $settings) {}

    public function isEnabled(): bool
    {
        $enabled = (bool) $this->settings->get('payment', 'razorpay_enabled', false);
        $keyId = $this->keyId();
        $secret = $this->keySecret();

        return $enabled && filled($keyId) && filled($secret);
    }

    public function keyId(): ?string
    {
        return config('services.razorpay.key')
            ?: $this->settings->get('payment', 'razorpay_key_id')
            ?: null;
    }

    public function keySecret(): ?string
    {
        return config('services.razorpay.secret') ?: null;
    }

    /**
     * @return array{id: string, amount: int, currency: string}
     */
    public function createOrder(Booking $booking): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Online payments are not configured.');
        }

        $amountPaise = (int) round(((float) $booking->price) * 100);

        $response = Http::withBasicAuth((string) $this->keyId(), (string) $this->keySecret())
            ->acceptJson()
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountPaise,
                'currency' => 'INR',
                'receipt' => $booking->reference,
                'notes' => [
                    'booking_reference' => $booking->reference,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Razorpay request failed: '.$response->body());
        }

        $data = $response->json();

        return [
            'id' => $data['id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'INR',
        ];
    }

    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        $secret = $this->keySecret();

        if (! $secret) {
            return false;
        }

        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

        return hash_equals($expected, $signature);
    }
}

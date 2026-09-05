<?php

namespace App\Services;

use App\Models\Coupon;
use InvalidArgumentException;

class CouponService
{
    public function findValid(string $code, float $orderAmount): Coupon
    {
        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])
            ->first();

        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            throw new InvalidArgumentException('This coupon code is invalid or expired.');
        }

        $discount = $coupon->discountFor($orderAmount);
        if ($discount <= 0) {
            throw new InvalidArgumentException('This coupon does not apply to the current order amount.');
        }

        return $coupon;
    }

    /**
     * @return array{coupon: Coupon, discount: float, total: float}
     */
    public function apply(string $code, float $orderAmount): array
    {
        $coupon = $this->findValid($code, $orderAmount);
        $discount = $coupon->discountFor($orderAmount);

        return [
            'coupon' => $coupon,
            'discount' => $discount,
            'total' => max(0, round($orderAmount - $discount, 2)),
        ];
    }
}

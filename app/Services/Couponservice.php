<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;

class CouponService
{
    /**
     * Validate a coupon code against the cart subtotal and customer identity.
     *
     * @return array{valid: bool, message?: string, coupon?: Coupon, discount?: float}
     */
    public function validate(
        string $code,
        float $subtotal,
        ?string $email = null,
        ?string $phone = null,
    ): array {
        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper(trim($code))])->first();

        if (!$coupon) {
            return $this->fail('Invalid coupon code.');
        }

        if (!$coupon->isUsable()) {
            return $this->fail('This coupon is no longer valid.');
        }

        if ($subtotal < (float) $coupon->minimum_amount) {
            return $this->fail(
                'Minimum order amount for this coupon is ৳' . number_format((float) $coupon->minimum_amount, 2) . '.'
            );
        }

        if (($email || $phone) && $this->exceedsPerUserLimit($coupon, $email, $phone)) {
            return $this->fail('You have already used this coupon the maximum number of times.');
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $this->calculateDiscount($coupon, $subtotal),
        ];
    }

    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'fixed') {
            return round(min((float) $coupon->value, $subtotal), 2);
        }

        $discount = $subtotal * ((float) $coupon->value / 100);

        if ($coupon->maximum_discount) {
            $discount = min($discount, (float) $coupon->maximum_discount);
        }

        return round($discount, 2);
    }

    private function exceedsPerUserLimit(Coupon $coupon, ?string $email, ?string $phone): bool
    {
        if (!$coupon->usage_per_user) {
            return false;
        }

        $usedByCustomer = Order::where('coupon_id', $coupon->id)
            ->where(function ($query) use ($email, $phone) {
                if ($email) {
                    $query->orWhere('email', $email);
                }

                if ($phone) {
                    $query->orWhere('phone', $phone);
                }
            })
            ->count();

        return $usedByCustomer >= $coupon->usage_per_user;
    }

    private function fail(string $message): array
    {
        return [
            'valid' => false,
            'message' => $message,
        ];
    }
}

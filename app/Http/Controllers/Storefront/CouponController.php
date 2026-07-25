<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CouponService $couponService,
    ) {}

    /**
     * Validate and apply a coupon code to the current cart session.
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
        ]);

        $subtotal = $this->cartService->subtotal();

        $result = $this->couponService->validate(
            $request->string('code')->toString(),
            $subtotal,
            $request->input('email'),
            $request->input('phone'),
        );

        if (!$result['valid']) {
            return response()->json([
                'message' => $result['message'],
            ], 422);
        }

        session([
            'applied_coupon' => [
                'code' => $result['coupon']->code,
                'discount' => $result['discount'],
            ],
        ]);

        return response()->json([
            'code' => $result['coupon']->code,
            'discount' => $result['discount'],
            'message' => 'Coupon applied successfully.',
        ]);
    }

    /**
     * Remove the currently applied coupon from the session.
     */
    public function remove(): JsonResponse
    {
        session()->forget('applied_coupon');

        return response()->json([
            'message' => 'Coupon removed.',
        ]);
    }
}

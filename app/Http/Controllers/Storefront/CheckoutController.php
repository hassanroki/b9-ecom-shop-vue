<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreCheckoutRequest;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Services\SslcommerzPaymentService;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
        private readonly SslcommerzPaymentService $paymentService,
        private readonly StripePaymentService $stripeService,
        private readonly CouponService $couponService,
    ) {}

    /**
     * Display the checkout page.
     */
    public function index(): Response|RedirectResponse
    {
        if ($this->cartService->totalQty() === 0) {
            return redirect()
                ->route('shop.cart')
                ->with('error', 'Your cart is empty. Add items before checkout.');
        }

        $delivery = config('shop.delivery');

        return Inertia::render('shop/Checkout', [
            'districts' => config('shop.districts'),
            'deliveryCharges' => [
                'insideDhaka' => (float) $delivery['inside_dhaka'],
                'outsideDhaka' => (float) $delivery['outside_dhaka'],
                'dhakaDistrict' => $delivery['dhaka_district'],
            ],
            'appliedCoupon' => session('applied_coupon'),
            'stripeExchangeRate' => (float) config('stripe.exchange_rate', 0.0084),
        ]);
    }

    /**
     * Place an order and complete checkout.
     */
    public function store(StoreCheckoutRequest $request): RedirectResponse|SymfonyResponse
    {
        $validated = $request->validated();

        // Re-validate the coupon server-side at the moment of order placement.
        // Never trust the discount amount computed on the client.
        $couponData = null;

        if (!empty($validated['coupon_code'])) {
            $result = $this->couponService->validate(
                $validated['coupon_code'],
                $this->cartService->subtotal(),
                $validated['email'] ?? null,
                $validated['phone'] ?? null,
            );

            if (!$result['valid']) {
                session()->forget('applied_coupon');

                return back()
                    ->withErrors(['coupon_code' => $result['message']])
                    ->withInput($request->except('coupon_code'));
            }

            $couponData = [
                'coupon_id' => $result['coupon']->id,
                'coupon_code' => $result['coupon']->code,
                'discount_amount' => $result['discount'],
            ];
        }

        $orderPayload = array_merge($validated, $couponData ?? []);

        if ($validated['payment_method'] === 'sslcommerz') {
            $order = $this->orderService->placeSslcommerzOrder($orderPayload);
            $result = $this->paymentService->initiate($order);

            if ($result['gateway_url'] === null) {
                return redirect()
                    ->route('shop.payments.failed', ['order' => $order->order_number])
                    ->with('error', 'Unable to start online payment. Please try again.');
            }

            if ($couponData) {
                $order->coupon->recordUsage();
                session()->forget('applied_coupon');
            }

            return Inertia::location($result['gateway_url']);
        }

        if ($validated['payment_method'] === 'stripe') {
            $order = $this->orderService->placeStripeOrder($orderPayload);
            $result = $this->stripeService->initiate($order);

            if ($result['checkout_url'] === null) {
                return redirect()
                    ->route('shop.payments.failed', ['order' => $order->order_number])
                    ->with('error', 'Unable to start Stripe payment. Please try again.');
            }

            if ($couponData) {
                $order->coupon->recordUsage();
                session()->forget('applied_coupon');
            }

            return Inertia::location($result['checkout_url']);
        }

        $order = $this->orderService->placeCodOrder($orderPayload);

        if ($couponData) {
            $order->coupon->recordUsage();
            session()->forget('applied_coupon');
        }

        return redirect()
            ->route('shop.orders.success')
            ->with('order', [
                'orderNumber' => $order->order_number,
                'total' => (float) $order->total,
                'discount' => (float) $order->discount_amount,
                'paymentLabel' => 'Cash on Delivery',
            ]);
    }
}

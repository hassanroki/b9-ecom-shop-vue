<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StripeCallbackController extends Controller
{
    public function __construct(
        private readonly StripePaymentService $stripeService,
    ) {}

    /**
     * Handle the redirect from Stripe after a successful payment.
     * Stripe appends ?session_id={CHECKOUT_SESSION_ID} to the success URL.
     */
    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('shop.payments.failed')
                ->with('error', 'Missing Stripe session ID.');
        }

        try {
            $payment = $this->stripeService->confirmSession((string) $sessionId);

            return redirect()->route('shop.payments.success', [
                'order' => $payment->order?->order_number,
            ]);
        } catch (ValidationException) {
            return redirect()->route('shop.payments.failed')
                ->with('error', 'Stripe payment could not be verified.');
        }
    }

    /**
     * Handle the redirect from Stripe when a user cancels payment.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $orderNumber = $request->query('order');

        if ($orderNumber) {
            $this->stripeService->cancelByOrderNumber((string) $orderNumber);
        }

        return redirect()->route('shop.payments.cancelled', [
            'order' => $orderNumber,
        ]);
    }
}

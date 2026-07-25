<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly StripePaymentService $stripeService,
    ) {}

    /**
     * Handle incoming Stripe webhook events.
     * This endpoint is excluded from CSRF verification.
     */
    public function handle(Request $request): Response
    {
        try {
            $this->stripeService->handleWebhook($request);

            return response('Webhook handled.', 200);
        } catch (ValidationException $e) {
            return response('Webhook signature verification failed.', 400);
        } catch (\Throwable $e) {
            return response('Webhook processing error: ' . $e->getMessage(), 500);
        }
    }
}

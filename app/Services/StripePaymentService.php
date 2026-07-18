<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * Initiate a Stripe Checkout Session for the given order.
     *
     * @return array{payment: Payment, checkout_url: string|null}
     */
    public function initiate(Order $order): array
    {
        if ($order->payment_status === 'paid') {
            throw ValidationException::withMessages([
                'order_id' => 'This order has already been paid.',
            ]);
        }

        // Reuse an existing initiated payment if one exists
        $existingPayment = $order->payments()
            ->where('status', 'initiated')
            ->where('gateway', 'stripe')
            ->latest()
            ->first();

        if ($existingPayment !== null) {
            $checkoutUrl = $existingPayment->raw_response['url'] ?? null;

            if ($checkoutUrl !== null) {
                return [
                    'payment' => $existingPayment,
                    'checkout_url' => $checkoutUrl,
                ];
            }
        }

        $transactionId = $this->generateTransactionId($order);

        $order->loadMissing('items');

        $productName = $order->items->first()?->product_name ?? "Order {$order->order_number}";
        $currency = strtolower(config('stripe.currency', 'usd'));
        $exchangeRate = (float) config('stripe.exchange_rate', 0.0084);
        $usdAmount = round((float) $order->total * $exchangeRate, 2);

        // Convert amount to the smallest currency unit (cents for USD)
        $amountInCents = (int) round($usdAmount * 100);

        // Build Stripe Checkout Session
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $amountInCents,
                        'product_data' => [
                            'name' => $productName,
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'client_reference_id' => $transactionId,
            'success_url' => route('shop.payments.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('shop.payments.stripe.cancel', ['order' => $order->order_number]),
            'customer_email' => $order->email,
            'metadata' => [
                'order_number' => $order->order_number,
                'transaction_id' => $transactionId,
            ],
        ]);

        // Create Payment record
        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'transaction_id' => $transactionId,
            'amount' => $usdAmount,
            'currency' => strtoupper($currency),
            'status' => 'initiated',
            'raw_response' => [
                'session_id' => $session->id,
                'url' => $session->url,
            ],
        ]);

        return [
            'payment' => $payment->fresh(),
            'checkout_url' => $session->url,
        ];
    }

    /**
     * Handle a Stripe webhook event (server-to-server).
     */
    public function handleWebhook(Request $request): void
    {
        $webhookSecret = config('stripe.webhook_secret');
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature', '');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            throw ValidationException::withMessages([
                'signature' => 'Invalid Stripe webhook signature.',
            ]);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleSessionCompleted($event->data->object),
            'checkout.session.expired' => $this->handleSessionExpired($event->data->object),
            'payment_intent.payment_failed' => null, // Handled via cancel_url redirect
            default => null,
        };
    }

    /**
     * Verify and confirm a Stripe Checkout Session after redirect (success URL).
     */
    public function confirmSession(string $sessionId): Payment
    {
        $session = StripeSession::retrieve($sessionId);

        $transactionId = $session->client_reference_id
            ?? $session->metadata['transaction_id']
            ?? null;

        if (!$transactionId) {
            throw ValidationException::withMessages([
                'session_id' => 'Cannot identify payment from Stripe session.',
            ]);
        }

        $payment = $this->findPaymentByTransactionId($transactionId);

        if ($payment->status === 'success') {
            return $payment->load(['order']);
        }

        if ($session->payment_status !== 'paid') {
            throw ValidationException::withMessages([
                'payment' => 'Stripe payment has not been completed yet.',
            ]);
        }

        return DB::transaction(function () use ($payment, $session): Payment {
            $payment->update([
                'status' => 'success',
                'val_id' => $session->payment_intent,
                'paid_at' => now(),
                'raw_response' => array_merge($payment->raw_response ?? [], [
                    'payment_intent' => $session->payment_intent,
                    'payment_status' => $session->payment_status,
                ]),
            ]);

            $payment->order?->update(['payment_status' => 'paid']);

            return $payment->fresh(['order']);
        });
    }

    /**
     * Mark a payment as cancelled (called from cancel_url redirect).
     */
    public function cancelByOrderNumber(string $orderNumber): ?Payment
    {
        $payment = Payment::query()
            ->whereHas('order', fn($q) => $q->where('order_number', $orderNumber))
            ->where('gateway', 'stripe')
            ->where('status', 'initiated')
            ->latest()
            ->first();

        if ($payment === null) {
            return null;
        }

        $payment->update(['status' => 'cancelled']);
        $payment->order?->update(['payment_status' => 'cancelled']);

        return $payment->fresh(['order']);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function handleSessionCompleted(object $session): void
    {
        $transactionId = $session->client_reference_id
            ?? $session->metadata['transaction_id']
            ?? null;

        if (!$transactionId) {
            return;
        }

        try {
            $payment = $this->findPaymentByTransactionId((string) $transactionId);

            if ($payment->status === 'success') {
                return;
            }

            DB::transaction(function () use ($payment, $session): void {
                $payment->update([
                    'status' => 'success',
                    'val_id' => $session->payment_intent,
                    'paid_at' => now(),
                    'raw_response' => array_merge($payment->raw_response ?? [], [
                        'payment_intent' => $session->payment_intent,
                        'payment_status' => $session->payment_status,
                    ]),
                ]);

                $payment->order?->update(['payment_status' => 'paid']);
            });
        } catch (ValidationException) {
            // Payment record not found — ignore
        }
    }

    private function handleSessionExpired(object $session): void
    {
        $transactionId = $session->client_reference_id
            ?? $session->metadata['transaction_id']
            ?? null;

        if (!$transactionId) {
            return;
        }

        try {
            $payment = $this->findPaymentByTransactionId((string) $transactionId);
            $payment->update(['status' => 'cancelled']);
            $payment->order?->update(['payment_status' => 'cancelled']);
        } catch (ValidationException) {
            // Ignore
        }
    }

    private function findPaymentByTransactionId(string $transactionId): Payment
    {
        if ($transactionId === '') {
            throw ValidationException::withMessages([
                'transaction_id' => 'Transaction ID is required.',
            ]);
        }

        $payment = Payment::query()
            ->where('transaction_id', $transactionId)
            ->where('gateway', 'stripe')
            ->first();

        if ($payment === null) {
            throw ValidationException::withMessages([
                'transaction_id' => 'Stripe payment not found for the given transaction ID.',
            ]);
        }

        return $payment;
    }

    private function generateTransactionId(Order $order): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $transactionId = sprintf(
                'STR-%s-%s',
                $order->order_number,
                strtoupper(substr(uniqid(), -8)),
            );

            if (!Payment::query()->where('transaction_id', $transactionId)->exists()) {
                return $transactionId;
            }
        }

        throw new RuntimeException('Unable to generate a unique Stripe transaction ID.');
    }
}

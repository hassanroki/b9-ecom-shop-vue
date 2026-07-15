<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use HasinHayder\Sslcommerz\Facades\Sslcommerz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SslCommerzController extends Controller
{
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
        ]);

        $order = Order::findOrFail($data['order_id']);

        if ((float) $order->total <= 0) {
            throw ValidationException::withMessages([
                'order_id' => 'The selected order must have a positive total amount.',
            ]);
        }

        $itemQty = max(1, (int) $order->items()->sum('quantity'));
        $paymentResponse = Sslcommerz::setOrder(
            (float) $order->total,
            (string) $order->order_number,
            'Order ' . $order->order_number,
            'ecommerce'
        )
            ->setCustomer(
                (string) $data['customer_name'],
                (string) $data['email'],
                (string) $data['phone'],
                (string) $data['address'],
                'Dhaka',
                'Dhaka',
                '1200',
                'Bangladesh'
            )
            ->setShippingInfo(
                $itemQty,
                (string) $data['address'],
                (string) $data['customer_name'],
                $order->district ?? 'Dhaka',
                $order->area ?? 'Dhaka',
                '1200',
                'Bangladesh'
            )
            ->setCallbackUrls(
                route('sslc.success'),
                route('sslc.failure'),
                route('sslc.cancel'),
                route('sslc.ipn')
            )
            ->setProductProfile(config('sslcommerz.product_profile', 'general'))
            ->makePayment();

        $payment = Payment::query()
            ->where('order_id', $order->id)
            ->latest()
            ->first();

        $paymentData = [
            'order_id' => $order->id,
            'gateway' => 'sslcommerz',
            'transaction_id' => (string) $order->order_number,
            'amount' => (float) $order->total,
            'currency' => config('sslcommerz.store.currency', 'BDT'),
            'status' => $paymentResponse->success() ? 'initiated' : 'failed',
            'raw_response' => $paymentResponse->toArray(),
        ];

        if ($payment) {
            $payment->update($paymentData);
        } else {
            $payment = Payment::create($paymentData);
        }

        $order->update([
            'payment_method' => 'sslcommerz',
            'payment_status' => $paymentResponse->success() ? 'pending' : 'failed',
            'status' => $paymentResponse->success() ? 'pending' : 'cancelled',
        ]);

        if ($paymentResponse->success() && $paymentResponse->gatewayPageURL()) {
            return response()->json([
                'success' => true,
                'message' => 'SSLCommerz payment initiated successfully.',
                'redirect_url' => $paymentResponse->gatewayPageURL(),
                'transaction_id' => $payment->transaction_id,
                'payment_id' => $payment->id,
                'order_id' => $order->id,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
            'message' => $paymentResponse->failedReason() ?? 'Unable to initialize SSLCommerz payment.',
            'transaction_id' => $payment->transaction_id,
            'order_id' => $order->id,
        ], Response::HTTP_BAD_REQUEST);
    }

    public function success(Request $request): JsonResponse
    {
        return $this->finalize($request, 'success', 'paid', 'processing');
    }

    public function failure(Request $request): JsonResponse
    {
        return $this->finalize($request, 'failed', 'failed', 'cancelled');
    }

    public function cancel(Request $request): JsonResponse
    {
        return $this->finalize($request, 'cancelled', 'cancelled', 'cancelled');
    }

    public function ipn(Request $request): JsonResponse
    {
        return $this->finalize($request, 'success', 'paid', 'processing');
    }

    private function finalize(Request $request, string $paymentStatus, string $orderPaymentStatus, string $orderStatus): JsonResponse
    {
        $transactionId = (string) $request->input('tran_id', $request->input('tran_id', ''));
        $order = Order::where('order_number', $transactionId)->first();

        if ($order === null) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], Response::HTTP_NOT_FOUND);
        }

        $payment = Payment::query()
            ->where('order_id', $order->id)
            ->where('transaction_id', $transactionId)
            ->latest()
            ->first();

        if ($payment === null) {
            $payment = $order->payments()->create([
                'transaction_id' => $transactionId,
                'amount' => (float) $order->total,
                'currency' => config('sslcommerz.store.currency', 'BDT'),
                'status' => 'initiated',
                'raw_response' => $request->all(),
            ]);
        }

        $payment->update([
            'val_id' => $request->input('val_id'),
            'status' => $paymentStatus,
            'card_type' => $request->input('card_type'),
            'bank_tran_id' => $request->input('bank_tran_id'),
            'store_amount' => $request->input('store_amount'),
            'raw_response' => array_merge((array) $payment->raw_response, $request->all()),
            'paid_at' => $paymentStatus === 'success' ? now() : null,
        ]);

        $order->update([
            'payment_method' => 'sslcommerz',
            'payment_status' => $orderPaymentStatus,
            'status' => $orderStatus,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment callback processed successfully.',
            'transaction_id' => $transactionId,
            'order_id' => $order->id,
            'payment_status' => $paymentStatus,
        ], Response::HTTP_OK);
    }
}

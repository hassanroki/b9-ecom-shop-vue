<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\ImageUrl;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with(['items.product.images' => fn($query) => $query->where('is_primary', true)])
            ->orderBy('placed_at', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function (Order $order): array {
                $orderArray = $order->toArray();

                $orderArray['items'] = $order->items->map(function ($item): array {
                    $itemArray = $item->toArray();
                    $itemArray['img'] = ImageUrl::resolve(
                        $item->product?->images->first()?->image_path
                    );
                    return $itemArray;
                })->all();

                return $orderArray;
            });

        return Inertia::render('customer/Dashboard', [
            'orders' => $orders,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
            ->with(['items.product'])
            ->orderBy('placed_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('customer/Dashboard', [
            'orders' => $orders,
        ]);
    }
}

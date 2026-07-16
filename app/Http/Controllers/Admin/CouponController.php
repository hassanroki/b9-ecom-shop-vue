<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $coupons = Coupon::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                match ($request->status) {
                    'active' => $query
                        ->where('is_active', true)
                        ->where(function ($q) {
                            $q->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', now());
                        })
                        ->where(function ($q) {
                            $q->whereNull('expires_at')
                                ->orWhere('expires_at', '>=', now());
                        }),

                    'inactive' => $query->where('is_active', false),

                    'expired' => $query
                        ->whereNotNull('expires_at')
                        ->where('expires_at', '<', now()),

                    'scheduled' => $query
                        ->whereNotNull('starts_at')
                        ->where('starts_at', '>', now()),

                    default => null,
                };
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/coupons/Index', [
            'coupons' => $coupons,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/coupons/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::create($request->validated());

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon): Response
    {
        return Inertia::render('admin/coupons/Show', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('admin/coupons/Edit', [
            'coupon' => [
                ...$coupon->toArray(),
                'starts_at' => $coupon->starts_at?->format('Y-m-d\TH:i'),
                'expires_at' => $coupon->expires_at?->format('Y-m-d\TH:i'),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}

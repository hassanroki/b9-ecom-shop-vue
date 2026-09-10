<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $reviews = Review::query()
            ->with(['product:id,name,slug', 'user:id,name,email', 'order:id,order_number'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($q) use ($search) {
                    $q->where('comment', 'like', "%{$search}%")
                        ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');

                if ($status === 'approved') {
                    $query->where('is_approved', true);
                } elseif ($status === 'pending') {
                    $query->where('is_approved', false);
                }
            })
            ->when($request->filled('rating'), function ($query) use ($request) {
                $query->where('rating', (int) $request->input('rating'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn(Review $review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_approved' => $review->is_approved,
                'approved_at' => $review->approved_at?->format('d M Y, h:i A'),
                'created_at' => $review->created_at->format('d M Y, h:i A'),
                'product' => $review->product ? [
                    'id' => $review->product->id,
                    'name' => $review->product->name,
                    'slug' => $review->product->slug,
                ] : null,
                'user' => $review->user ? [
                    'name' => $review->user->name,
                    'email' => $review->user->email,
                ] : null,
                'order_number' => $review->order?->order_number,
            ]);

        return Inertia::render('admin/review/Index', [
            'reviews' => $reviews,
            'filters' => $request->only(['search', 'status', 'rating']),
            'summary' => [
                'total' => Review::count(),
                'pending' => Review::where('is_approved', false)->count(),
                'approved' => Review::where('is_approved', true)->count(),
            ],
        ]);
    }

    public function approve(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);

        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Review approved.');
    }

    public function reject(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);

        $review->update([
            'is_approved' => false,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Review set to pending.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Review::findOrFail($id)->delete();

        return back()->with('success', 'Review deleted.');
    }
}

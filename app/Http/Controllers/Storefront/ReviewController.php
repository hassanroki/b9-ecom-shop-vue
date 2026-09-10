<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreReviewRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function storeReview(StoreReviewRequest $request): RedirectResponse
    {
        Review::create([
            'product_id'  => $request->validated('product_id'),
            'user_id'     => Auth::id(),
            'order_id'    => $request->input('order_id'),
            'rating'      => $request->validated('rating'),
            'comment'     => $request->validated('comment'),
            'is_approved' => false,
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted and is pending approval.');
    }
}

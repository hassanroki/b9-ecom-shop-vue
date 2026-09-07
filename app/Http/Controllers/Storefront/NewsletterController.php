<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreNewsletterSubscriberRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;

class NewsletterController extends Controller
{
    public function store(StoreNewsletterSubscriberRequest $request): JsonResponse
    {
        NewsletterSubscriber::create([
            'email' => $request->validated('email'),
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Thanks for subscribing! Check your inbox for updates.',
        ]);
    }
}

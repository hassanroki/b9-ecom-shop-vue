<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('shop/Wishlist');
    }
}

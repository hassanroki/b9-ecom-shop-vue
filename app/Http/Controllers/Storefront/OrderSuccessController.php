<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderSuccessController extends Controller
{
    /**
     * Display the order success page.
     */
    public function __invoke(): Response
    {
        return Inertia::render('shop/OrderSuccess');
    }
}

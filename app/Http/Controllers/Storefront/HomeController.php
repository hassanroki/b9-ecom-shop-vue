<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the storefront homepage.
     */
    public function __invoke(): Response
    {
        return Inertia::render('shop/Home');
    }
}

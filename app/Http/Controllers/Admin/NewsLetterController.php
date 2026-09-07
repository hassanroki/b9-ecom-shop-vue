<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsLetterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NewsLetterController extends Controller
{
public function index(Request $request): InertiaResponse
{
    $search = $request->string('search')->trim()->value();

    $newsletters = NewsLetterSubscriber::query()
        ->when($search, fn ($query) => $query->where('email', 'like', "%{$search}%"))
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('admin/newsletter/Index', [
        'newsletters' => $newsletters,
        'filters' => [
            'search' => $search,
        ],
    ]);
}

    public function download(Request $request): StreamedResponse
    {
        $search = $request->string('search')->trim()->value();

        $newsletters = NewsLetterSubscriber::query()
            ->when($search, fn ($query) => $query->where('email', 'like', "%{$search}%"))
            ->latest()
            ->get(['email', 'created_at']);

        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d-His') . '.csv';

        return Response::streamDownload(function () use ($newsletters) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Subscribed At']);

            foreach ($newsletters as $newsletter) {
                fputcsv($handle, [
                    $newsletter->email,
                    $newsletter->created_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        NewsLetterSubscriber::findOrFail($id)->delete();

        return back()->with('success', 'Subscriber removed successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $items = GalleryItem::query()->active()->get();
        $featured = GalleryItem::query()->active()->featured()->get();

        return view('public.gallery.index', [
            'items' => $items,
            'featured' => $featured,
            'seoTitle' => 'Car Wash Gallery Punjab | Before & After Results',
            'seoDescription' => 'See real before-and-after results from steam wash, detailing, paint correction, ceramic coating, and PPF at Diamond Steam Car Wash in Punjab.',
        ]);
    }
}

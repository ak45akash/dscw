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
            'seoTitle' => 'Before & After Gallery | Diamond Steam Car Wash',
            'seoDescription' => 'See real transformation results from our car wash, detailing, paint correction, ceramic coating, and PPF services in Mumbai.',
        ]);
    }
}

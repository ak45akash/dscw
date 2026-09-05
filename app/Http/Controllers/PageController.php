<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return $this->show('about-us');
    }

    public function privacy(): View
    {
        return $this->show('privacy-policy');
    }

    public function terms(): View
    {
        return $this->show('terms-and-conditions');
    }

    public function show(string $slug): View
    {
        $page = Page::query()->active()->where('slug', $slug)->firstOrFail();

        return view('public.pages.show', [
            'page' => $page,
            'seoTitle' => $page->meta_title ?? $page->title,
            'seoDescription' => $page->meta_description,
        ]);
    }
}

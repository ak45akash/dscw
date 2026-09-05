<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Service;
use App\Models\Testimonial;
use App\Services\SettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function __invoke(): View
    {
        $business = $this->settings->getGroup('business');

        return view('public.home', [
            'businessName' => $business['business_name'] ?? config('dscw.business.name'),
            'tagline' => $business['tagline'] ?? config('dscw.business.tagline'),
            'city' => $business['city'] ?? 'Mumbai',
            'featuredServices' => Service::query()->active()->with('category')->orderBy('price')->take(6)->get(),
            'latestPosts' => BlogPost::query()->published()->with('category')->latest('published_at')->take(3)->get(),
            'testimonials' => Testimonial::query()->active()->featured()->take(4)->get(),
        ]);
    }
}

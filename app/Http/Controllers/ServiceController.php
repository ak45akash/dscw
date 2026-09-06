<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::query()
            ->active()
            ->with(['services' => fn ($q) => $q->active()->orderBy('display_order')])
            ->orderBy('display_order')
            ->get();

        $featured = Service::query()->active()->featured()->with('category')->get();

        return view('public.services.index', [
            'categories' => $categories,
            'featured' => $featured,
            'seoTitle' => 'Car Wash Services Punjab | Steam, Detailing & Coating',
            'seoDescription' => 'Explore steam wash, interior detailing, ceramic coating, and PPF services at Diamond Steam Car Wash in Sector 66, SAS Nagar and Matour, Punjab.',
        ]);
    }

    public function show(string $slug): View
    {
        $service = Service::query()
            ->active()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        $related = Service::query()
            ->active()
            ->where('id', '!=', $service->id)
            ->when($service->service_category_id, fn ($q) => $q->where('service_category_id', $service->service_category_id))
            ->take(3)
            ->get();

        return view('public.services.show', [
            'service' => $service,
            'related' => $related,
            'seoTitle' => $service->meta_title ?? $service->name.' | Diamond Steam Car Wash',
            'seoDescription' => $service->meta_description ?? $service->short_description,
        ]);
    }
}

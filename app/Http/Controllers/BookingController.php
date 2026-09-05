<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $services = Service::query()->active()->orderBy('display_order')->get();

        return view('public.booking.index', [
            'services' => $services,
            'seoTitle' => 'Book Now | Diamond Steam Car Wash',
            'seoDescription' => 'Book your car wash, steam cleaning, detailing, or coating service online. Choose your service and preferred time slot at Diamond Steam Car Wash Mumbai.',
        ]);
    }
}

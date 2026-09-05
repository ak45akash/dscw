<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use App\Support\AdminNavigation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'navigation' => AdminNavigation::visibleForUser(auth()->user()),
            'stats' => [
                'today_bookings' => Booking::query()->today()->active()->count(),
                'pending_bookings' => Booking::query()->where('status', Booking::STATUS_PENDING)->count(),
                'confirmed_bookings' => Booking::query()->where('status', Booking::STATUS_CONFIRMED)->count(),
                'completed_bookings' => Booking::query()->where('status', Booking::STATUS_COMPLETED)->count(),
            ],
            'counts' => [
                'services' => Service::query()->count(),
                'locations' => Location::query()->count(),
            ],
        ]);
    }
}

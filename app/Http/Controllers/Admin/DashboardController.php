<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminNavigation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'navigation' => AdminNavigation::visibleForUser(auth()->user()),
            'stats' => [
                'today_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'completed_bookings' => 0,
            ],
        ]);
    }
}

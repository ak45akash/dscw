<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookings) {}

    public function index(Request $request): View
    {
        $query = Booking::query()->with(['location', 'service'])->latest('booking_date')->latest('start_time');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->integer('location_id'));
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->string('date'));
        }

        return view('admin.bookings.index', [
            'bookings' => $query->paginate(20)->withQueryString(),
            'locations' => Location::query()->orderBy('display_order')->get(),
            'filters' => $request->only(['status', 'location_id', 'date']),
            'title' => 'All Bookings',
        ]);
    }

    public function today(): View
    {
        $bookings = Booking::query()
            ->with(['location', 'service'])
            ->today()
            ->orderBy('start_time')
            ->paginate(50);

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'locations' => Location::query()->orderBy('display_order')->get(),
            'filters' => ['date' => today()->toDateString()],
            'title' => "Today's Bookings",
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['location', 'service']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,no_show'],
        ]);

        $this->bookings->updateStatus($booking, $data['status']);

        return back()->with('success', 'Booking status updated.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function bookings(Request $request): View|StreamedResponse
    {
        $this->authorizePermission('reports.view');

        [$from, $to] = $this->dateRange($request);
        $query = $this->bookingQuery($request, $from, $to);

        if ($request->boolean('export')) {
            return $this->csvExport($query->with(['location', 'service'])->orderBy('booking_date')->orderBy('start_time')->get(), 'booking-report');
        }

        $bookings = (clone $query)->with(['location', 'service'])
            ->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->paginate(30)
            ->withQueryString();

        $byStatus = (clone $query)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byDay = (clone $query)
            ->select('booking_date', DB::raw('count(*) as total'))
            ->groupBy('booking_date')
            ->orderBy('booking_date')
            ->get();

        return view('admin.reports.bookings', [
            'bookings' => $bookings,
            'byStatus' => $byStatus,
            'byDay' => $byDay,
            'locations' => Location::query()->orderBy('name')->get(),
            'filters' => array_merge($request->only(['location_id', 'status', 'payment_status']), [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ]),
            'total' => (clone $query)->count(),
        ]);
    }

    public function revenue(Request $request): View|StreamedResponse
    {
        $this->authorizePermission('reports.view');

        [$from, $to] = $this->dateRange($request);
        $base = $this->bookingQuery($request, $from, $to)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED]);

        $paidQuery = (clone $base)->where('payment_status', Booking::PAYMENT_PAID);
        $unpaidAtLocation = (clone $base)
            ->where('payment_method', Booking::PAYMENT_AT_LOCATION)
            ->where('payment_status', '!=', Booking::PAYMENT_PAID)
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED]);

        if ($request->boolean('export')) {
            return $this->csvExport(
                $paidQuery->with(['location', 'service'])->orderBy('booking_date')->get(),
                'revenue-report'
            );
        }

        $paidTotal = (float) (clone $paidQuery)->sum('price');
        $unpaidTotal = (float) (clone $unpaidAtLocation)->sum('price');

        $byDay = (clone $paidQuery)
            ->select('booking_date', DB::raw('sum(price) as total'), DB::raw('count(*) as count'))
            ->groupBy('booking_date')
            ->orderBy('booking_date')
            ->get();

        $byService = (clone $paidQuery)
            ->select('service_id', DB::raw('sum(price) as total'), DB::raw('count(*) as count'))
            ->groupBy('service_id')
            ->get()
            ->each(function ($row) {
                $row->service_name = Service::query()->whereKey($row->service_id)->value('name') ?? '—';
            });

        $byLocation = (clone $paidQuery)
            ->select('location_id', DB::raw('sum(price) as total'), DB::raw('count(*) as count'))
            ->groupBy('location_id')
            ->get()
            ->each(function ($row) {
                $row->location_name = Location::query()->whereKey($row->location_id)->value('name') ?? '—';
            });

        return view('admin.reports.revenue', [
            'paidTotal' => $paidTotal,
            'unpaidTotal' => $unpaidTotal,
            'paidCount' => (clone $paidQuery)->count(),
            'unpaidCount' => (clone $unpaidAtLocation)->count(),
            'byDay' => $byDay,
            'byService' => $byService,
            'byLocation' => $byLocation,
            'locations' => Location::query()->orderBy('name')->get(),
            'filters' => array_merge($request->only(['location_id', 'service_id']), [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ]),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function dateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from'))->startOfDay()
            : now()->startOfMonth()->startOfDay();
        $to = $request->filled('to')
            ? Carbon::parse($request->string('to'))->endOfDay()
            : now()->endOfDay();

        return [$from, $to];
    }

    private function bookingQuery(Request $request, Carbon $from, Carbon $to)
    {
        return Booking::query()
            ->whereDate('booking_date', '>=', $from->toDateString())
            ->whereDate('booking_date', '<=', $to->toDateString())
            ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->integer('location_id')))
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')));
    }

    private function csvExport($bookings, string $basename): StreamedResponse
    {
        $filename = $basename.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($bookings) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Date', 'Time', 'Customer', 'Service', 'Location', 'Status', 'Payment', 'Price']);
            foreach ($bookings as $booking) {
                fputcsv($out, [
                    $booking->reference,
                    $booking->booking_date?->toDateString(),
                    substr((string) $booking->start_time, 0, 5),
                    $booking->customer_name,
                    $booking->service?->name,
                    $booking->location?->name,
                    $booking->status,
                    $booking->payment_status,
                    $booking->price,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function authorizePermission(string $permission): void
    {
        abort_unless(auth()->user()?->hasPermission($permission), 403);
    }
}

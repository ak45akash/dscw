<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $enquiries = ContactEnquiry::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function show(ContactEnquiry $enquiry): View
    {
        if ($enquiry->status === 'new') {
            $enquiry->update(['status' => 'read']);
        }

        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function updateStatus(Request $request, ContactEnquiry $enquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,archived'],
        ]);

        $enquiry->update($data);

        return back()->with('success', 'Enquiry status updated.');
    }
}

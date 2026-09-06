<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactEnquiryMail;
use App\Models\ContactEnquiry;
use App\Models\Faq;
use App\Models\Location;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function index(): View
    {
        $business = $this->settings->getGroup('business');

        return view('public.contact.index', [
            'business' => $business,
            'locations' => Location::query()->active()->orderBy('display_order')->get(),
            'faqs' => Faq::query()->active()->where('category', 'Booking')->take(3)->get(),
            'seoTitle' => 'Contact Diamond Steam | Sector 66 & Matour Punjab',
            'seoDescription' => 'Visit Diamond Steam Car Wash at Plot 589, Sector 66 near Bestech Mall, SAS Nagar, or Matour, Punjab. Call, WhatsApp, or message us for bookings and quotes.',
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $enquiry = ContactEnquiry::query()->create($request->validated());

        $adminEmail = $this->settings->get('business', 'email');
        if (filled($adminEmail)) {
            try {
                Mail::to($adminEmail)->send(new ContactEnquiryMail($enquiry));
            } catch (Throwable) {
                // Do not fail the contact form if mail is misconfigured.
            }
        }

        return back()->with('success', 'Thank you for your message. Our team will respond within 24 hours.');
    }
}

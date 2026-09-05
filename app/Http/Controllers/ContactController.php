<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactEnquiry;
use App\Models\Faq;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function index(): View
    {
        $business = $this->settings->getGroup('business');

        return view('public.contact.index', [
            'business' => $business,
            'faqs' => Faq::query()->active()->where('category', 'Booking')->take(3)->get(),
            'seoTitle' => 'Contact Us | Diamond Steam Car Wash Mumbai',
            'seoDescription' => 'Get in touch with Diamond Steam Car Wash. Call, email, or send us a message for bookings, quotes, and car care enquiries in Mumbai.',
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        ContactEnquiry::query()->create($request->validated());

        return back()->with('success', 'Thank you for your message. Our team will respond within 24 hours.');
    }
}

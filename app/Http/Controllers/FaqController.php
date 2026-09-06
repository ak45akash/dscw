<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->active()->get()->groupBy('category');

        return view('public.faq.index', [
            'faqGroups' => $faqs,
            'seoTitle' => 'FAQ | Steam Car Wash Booking & Services Punjab',
            'seoDescription' => 'Answers about steam car wash booking, pricing, ceramic coating, PPF, and care at Diamond Steam Car Wash locations in Sector 66 and Matour, Punjab.',
        ]);
    }
}

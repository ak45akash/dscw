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
            'seoTitle' => 'Frequently Asked Questions | Diamond Steam Car Wash',
            'seoDescription' => 'Find answers about our car wash services, booking process, pricing, ceramic coating, PPF, and more at Diamond Steam Car Wash Mumbai.',
        ]);
    }
}

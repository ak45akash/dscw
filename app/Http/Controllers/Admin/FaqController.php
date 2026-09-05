<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct(private HtmlSanitizer $htmlSanitizer) {}

    public function index(): View
    {
        $faqs = Faq::query()->orderBy('display_order')->orderBy('id')->paginate(30);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.faqs.form', [
            'faq' => new Faq(['is_active' => true, 'display_order' => 0]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::query()->create($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.form', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        return [
            ...$data,
            'answer' => $this->htmlSanitizer->sanitize($data['answer']),
            'category' => $data['category'] ?? null,
            'display_order' => (int) ($data['display_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::query()->latest()->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.form', [
            'coupon' => new Coupon(['type' => Coupon::TYPE_PERCENT, 'is_active' => true, 'value' => 10]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::query()->create($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'type' => ['required', Rule::in([Coupon::TYPE_PERCENT, Coupon::TYPE_FIXED])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($data['type'] === Coupon::TYPE_PERCENT && (float) $data['value'] > 100) {
            $data['value'] = 100;
        }

        return [
            ...$data,
            'code' => strtoupper(trim($data['code'])),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}

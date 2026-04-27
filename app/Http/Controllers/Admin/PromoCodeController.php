<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        $codes = PromoCode::latest()->paginate(20);
        return view('admin.promo-codes.index', compact('codes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'description' => 'nullable|string|max:255',
        ]);

        PromoCode::create($request->validated() + ['is_active' => true]);
        return back()->with('success', 'Code promo créé.');
    }

    public function toggle(PromoCode $promoCode)
    {
        $promoCode->update(['is_active' => !$promoCode->is_active]);
        return back()->with('success', 'Code promo mis à jour.');
    }

    public function destroy(PromoCode $promoCode)
    {
        $promoCode->delete();
        return back()->with('success', 'Code promo supprimé.');
    }
}

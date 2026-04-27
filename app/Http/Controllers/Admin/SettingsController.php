<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'deposit_percentage' => 'required|integer|min:1|max:100',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone' => 'required|string|max:20',
            'company_email' => 'required|email',
            'company_siret' => 'nullable|string|max:20',
            'cancellation_hours' => 'required|integer|min:1',
            'invoice_prefix' => 'nullable|string|max:10',
            'cgv_text' => 'nullable|string',
            'contract_message_default' => 'nullable|string',
            'invoice_message_default' => 'nullable|string',
            'inspection_message_default' => 'nullable|string',
        ]);

        foreach ($request->except('_token', '_method') as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Paramètres sauvegardés.');
    }
}

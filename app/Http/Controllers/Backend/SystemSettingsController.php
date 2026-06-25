<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('pages.backend.settings.system', compact('settings'));
    }
    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'nullable|string|max:50',
            'telegram_username' => 'nullable|string|max:100',
            'support_email' => 'nullable|email|max:150',
        ], [], [
            'whatsapp_number' => __('WhatsApp Numarası'),
            'telegram_username' => __('Telegram Kullanıcı Adı'),
            'support_email' => __('Destek E-Posta Adresi')
        ]);

        $data = $request->except('_token');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('system_app_name');

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }
}

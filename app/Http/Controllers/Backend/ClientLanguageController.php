<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientLanguageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->user_type !== 'Client' || !$user->client) {
            abort(403);
        }

        // Only languages that were assigned to the client by the admin
        $clientLanguages = $user->client->languages()->get();

        return view('pages.backend.languages.index', compact('clientLanguages'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'Client' || !$user->client) {
            abort(403);
        }

        $request->validate([
            'languages' => 'nullable|array',
            'languages.*.is_active' => 'nullable|boolean',
            'default_language' => 'required|string|exists:client_languages,code',
            'auto_translate' => 'required|boolean',
        ]);

        $clientLangs = $user->client->languages;
        
        $submittedLangs = $request->input('languages', []);
        $defaultLang = $request->input('default_language');

        foreach ($clientLangs as $clientLang) {
            $isActive = isset($submittedLangs[$clientLang->code]['is_active']);
            $isDefault = ($clientLang->code === $defaultLang);

            // If it's default, it must be active
            if ($isDefault) {
                $isActive = true;
            }

            $clientLang->update([
                'is_active' => $isActive,
                'is_default' => $isDefault,
            ]);
        }

        $user->client->update([
            'auto_translate' => $request->boolean('auto_translate')
        ]);

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClientSiteSettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->user_type !== 'Client') {
            abort(403);
        }

        return view('pages.backend.client_site_settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if ($user->user_type !== 'Client') {
            abort(403);
        }

        if ($user->client) {
            $updateData = [];

            // Contact
            if ($request->has('address')) $updateData['address'] = $request->input('address');
            if ($request->has('additional_address')) $updateData['additional_address'] = $request->input('additional_address');
            if ($request->has('phone')) $updateData['phone'] = $request->input('phone');
            if ($request->has('fax')) $updateData['fax'] = $request->input('fax');
            if ($request->has('mobile')) $updateData['mobile'] = $request->input('mobile');
            if ($request->has('additional_contact')) $updateData['additional_contact'] = $request->input('additional_contact');
            if ($request->has('coordinates')) $updateData['coordinates'] = $request->input('coordinates');

            // Social
            if ($request->has('instagram')) $updateData['instagram'] = $request->input('instagram');
            if ($request->has('facebook')) $updateData['facebook'] = $request->input('facebook');
            if ($request->has('whatsapp')) $updateData['whatsapp'] = $request->input('whatsapp');

            // Analytics
            if ($request->has('google_analytics_code')) $updateData['google_analytics_code'] = $request->input('google_analytics_code');

            if (!empty($updateData)) {
                $user->client->update($updateData);
            }
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }
}

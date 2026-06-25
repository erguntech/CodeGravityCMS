<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WelcomeMessage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class WelcomeMessageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:welcome_message.view', only: ['edit']),
            new Middleware('can:welcome_message.manage', only: ['update']),
        ];
    }

    public function edit()
    {
        $welcomeMessage = WelcomeMessage::first() ?: new WelcomeMessage();
        return view('pages.backend.welcome_message.edit', compact('welcomeMessage'));
    }

    public function update(Request $request)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        
        $welcomeMessage = WelcomeMessage::first();
        $imageRule = ($welcomeMessage && $welcomeMessage->image) ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';

        $request->validate([
            'title' => 'nullable|array',
            'title.*' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'status' => 'required|in:active,passive',
            'image' => $imageRule,
        ], [], [
            'title' => __('Açılış Mesajı Başlığı'),
            'title.' . $clientDefaultLang => __('Açılış Mesajı Başlığı'),
            'description' => __('Açılış Mesajı Açıklaması'),
            'status' => __('Durum'),
            'image' => __('Açılış Mesajı Resmi'),
        ]);

        $data = $request->except(['_token', 'image']);

        if ($request->hasFile('image')) {
            if ($welcomeMessage && $welcomeMessage->image) {
                Storage::disk('public')->delete($welcomeMessage->image);
            }
            $data['image'] = $request->file('image')->store('welcome_messages', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('welcome_message');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        if ($welcomeMessage) {
            $welcomeMessage->update($data);
        } else {
            WelcomeMessage::create($data);
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }
}

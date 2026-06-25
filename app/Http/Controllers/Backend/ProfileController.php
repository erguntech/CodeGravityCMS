<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('pages.backend.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => '@ Mevcut Şifre alanı zorunludur.',
            'current_password.current_password' => '@ Mevcut şifreniz hatalı.',
            'password.required' => '@ Yeni Şifre alanı zorunludur.',
            'password.confirmed' => '@ Yeni Şifre uyuşmuyor.',
            'password.min' => '@ Şifre en az 8 karakter olmalıdır.',
        ]);


        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }
}

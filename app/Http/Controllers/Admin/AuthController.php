<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CampaignSetting;

class AuthController extends Controller
{
    /**
     * Show Admin Login Form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $settings = CampaignSetting::first();
        return view('admin.auth.login', compact('settings'));
    }

    /**
     * Process Admin Login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'স্বাগতম! আপনি সফলভাবে অ্যাডমিন প্যানেলে লগইন করেছেন।');
        }

        return back()->withErrors([
            'email' => 'প্রদত্ত ইমেইল বা পাসওয়ার্ড সঠিক নয়। অনুগ্রহ করে পুনরায় চেষ্টা করুন।',
        ])->onlyInput('email');
    }

    /**
     * Process Admin Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'আপনি সফলভাবে লগআউট হয়েছেন।');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Online;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LogController extends Controller
{
    public function in(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {
            if (Hash::needsRehash(Auth::user()->password)) {
                $request->user()->password = Hash::make($credentials['password']);
                $request->user()->save();
            }

            $request->session()->regenerate();

            $this->flashMessage('success', 'log.login_successful');

            return back();
        }

        $this->flashMessage('error', 'log.wrong_credentials');

        return back()->onlyInput('email');
    }

    public function out(Request $request): RedirectResponse
    {
        $online = Online::firstWhere('user', Auth::id());
        $request->user()->lastvisit = $online->time;
        $request->user()->save();
        $online->delete();

        Auth::logoutCurrentDevice();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $this->flashMessage('success', 'log.logout_successful');

        return back();
    }

    public function forgotPassword()
    {
        return view('register.start');
    }

    public function newPassword()
    {
        return view('register.start');
    }
}

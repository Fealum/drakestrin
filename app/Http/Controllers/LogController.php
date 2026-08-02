<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\Core\Online;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        $online = Online::firstWhere('user_id', Auth::id());
        $request->user()->lastvisit = $online?->time ?? now();
        $request->user()->save();
        $online?->delete();

        Auth::logoutCurrentDevice();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $this->flashMessage('success', 'log.logout_successful');

        return back();
    }

    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate(
                [
                    'email' => 'required',
                ],
                [
                    'email.required' => 'Bitte gib die Email-Adresse ein, mit der Dein Nutzerkonto registriert ist.',
                ],
            );

            $emailExists = Validator::make($request->all(), [
                'email' => 'exists:App\Models\User',
            ]);

            if ($emailExists->fails()) {
                return view('log.password_link_sent');
            }

            $user = User::firstWhere('email', $validated['email']);
            $key = $this->makeKey($user->email, $user->password, $user->lastvisit);

            Mail::to($user->email)->send(new ForgotPassword($user->email, $key));

            return view('log.password_link_sent');
        }

        return view('log.forgot_password');
    }

    public function newPassword(Request $request, string $email, string $key)
    {
        $user = User::firstWhere('email', $email);

        if (! $user) {
            $this->flashMessage('error', 'log.email_not_in_system');

            return redirect()->route('log.forgot_password');
        }

        if ($key !== $this->makeKey($email, $user->password, $user->lastvisit)) {
            $this->flashMessage('error', 'log.invalid_key', ['email' => $email]);

            return redirect()->route('log.forgot_password');
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate(
                [
                    'password' => 'required|confirmed',
                ],
                [
                    'password.required' => 'Bitte gib ein Passwort ein.',
                    'password.confirmed' => 'Die beiden eingegebenen Passwörter stimmen nicht überein.',
                ],
            );

            $user->password = Hash::make($validated['password']);
            $user->save();

            Auth::login($user, true);

            $this->flashMessage('success', 'log.password_changed');

            return redirect()->route('index');
        }

        return view('log.new_password', [
            'email' => $email,
            'key' => $key,
        ]);
    }

    private function makeKey(string $email, string $password, Carbon $lastVisit): string
    {
        return md5(config('app.key') . $email . config('app.key') . $password . config('app.key') . $lastVisit->timestamp . config('app.key'));
    }
}

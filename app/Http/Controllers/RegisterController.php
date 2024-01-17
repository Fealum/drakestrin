<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Mail\EmailValidation;
use App\Models\User;
use App\Models\ValidEmail;
use App\Models\Group;

class RegisterController extends Controller
{
    public function start()
    {
        return view('register.start');
    }

    public function validation(Request $request): RedirectResponse|View
    {
        $validEmail = Validator::make(
            $request->all(),
            [
                'email' => 'bail|required|email:rfc,dns',
            ],
            [
                'email.required' => 'Bitte gebe eine Email-Adresse an.',
                'email.email' => 'Bitte gebe eine gültige Email-Adresse an.',
            ],
        );

        if ($validEmail->fails()) {
            return redirect()->route('register')
                ->withErrors($validEmail)
                ->withInput();
        }

        $this->clearValidEmails();

        $uniqueEmail = Validator::make($request->all(), [
            'email' => 'bail|unique:App\Models\User|unique:App\Models\ValidEmail',
        ]);

        if ($uniqueEmail->fails()) {
            return view('register.validation');
        }

        $newValidEmail = ValidEmail::create([
            'email' => $uniqueEmail->safe()->only('email')['email'],
            'valid_until' => now()->addMinutes(config('auth.valid_email_timeout')),
        ]);

        $key = $this->makeKey($newValidEmail->email, $newValidEmail->valid_until);

        Mail::to($newValidEmail->email)->send(new EmailValidation($newValidEmail->email, $key));

        return view('register.validation', ['email' => $newValidEmail->email]);
    }

    public function registration(Request $request, string $email, string $key): RedirectResponse|View
    {
        $this->clearValidEmails();

        $validEmail = ValidEmail::where('email', $email)->first();

        if (!$validEmail) {
            $this->flashMessage('error', 'register.email_not_in_system');
            return redirect()->route('register');
        }

        if ($key !== $this->makeKey($email, $validEmail->valid_until)) {
            $this->flashMessage('error', 'register.invalid_key', ['email' => $email]);
            return redirect()->route('register');
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate(
                [
                    'name' => 'required|unique:App\Models\User',
                    'password' => 'required|confirmed',
                    'terms' => 'accepted',
                ],
                [
                    'name.required' => 'Bitte gib einen Nutzernamen ein.',
                    'name.unique' => 'Dieser Nutzername wird bereits für ein Nutzerkonto verwendet.',
                    'password.required' => 'Bitte gib ein Passwort ein.',
                    'password.confirmed' => 'Die beiden eingegebenen Passwörter stimmen nicht überein.',
                    'terms.accepted' => 'Um die Registrierung abzuschließen, musst Du den Nutzungsbedingungen zustimmen.',
                ],
            );

            $now = Carbon::now();

            $newUser = User::create([
                'name' => $validated['name'],
                'email' => $email,
                'password' => Hash::make($validated['password']),
                'usertext' => '',
                'regdate' => $now,
                'lastvisit' => $now,
                'lastactivity' => $now,
            ]);

            $defaultGroup = Group::find(1);
            $newUser->groups()->attach($defaultGroup);

            Auth::login($newUser, true);

            $validEmail->delete();

            return redirect()->route('register.welcome');
        }

        return view('register.registration', [
            'email' => $email,
            'key' => $key,
        ]);
    }

    public function welcome(): View
    {
        return view('register.welcome');
    }

    private function makeKey(string $email, Carbon $validUntil): string
    {
        return md5(config('app.key') . $email . config('app.key') . $validUntil->timestamp . config('app.key'));
    }

    private function clearValidEmails(): void
    {
        ValidEmail::where('valid_until', '<', now()->timestamp)->delete();
    }
}

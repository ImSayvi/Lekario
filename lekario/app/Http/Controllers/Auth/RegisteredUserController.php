<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9,15}$/', 'unique:'.User::class],
            'pesel' => ['required', 'string', 'size:11', 'regex:/^[0-9]{11}$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            'first_name.required' => 'Pole imię jest wymagane.',
            'last_name.required' => 'Pole nazwisko jest wymagane.',
            'email.required' => 'Pole email jest wymagane.',
            'email.unique' => 'Ten adres email jest już zajęty.',
            'phone.required' => 'Pole numer telefonu jest wymagane.',
            'phone.regex' => 'Numer telefonu musi zawierać tylko cyfry (9-15 znaków).',
            'phone.unique' => 'Ten numer telefonu jest już zarejestrowany.',
            'pesel.required' => 'Pole PESEL jest wymagane.',
            'pesel.size' => 'PESEL musi składać się z dokładnie 11 cyfr.',
            'pesel.regex' => 'PESEL musi zawierać tylko cyfry.',
            'pesel.unique' => 'Ten numer PESEL jest już zarejestrowany.',
            'password.required' => 'Pole hasło jest wymagane.',
            'password.confirmed' => 'Hasła nie są zgodne.',
            'terms.accepted' => 'Musisz zaakceptować regulamin i politykę prywatności.',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'pesel' => $request->pesel,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
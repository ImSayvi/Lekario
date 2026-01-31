<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('patient.settings.index', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Podaj obecne hasło',
            'current_password.current_password' => 'Obecne hasło jest nieprawidłowe',
            'password.required' => 'Podaj nowe hasło',
            'password.confirmed' => 'Hasła nie są identyczne',
            'password.min' => 'Hasło musi mieć co najmniej 8 znaków',
        ]);

        try {
            $user = Auth::user();
            
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            Log::info('Password changed', ['user_id' => $user->id]);

            return redirect()->route('settings')
                ->with('success', 'Hasło zostało zmienione pomyślnie.');

        } catch (\Exception $e) {
            Log::error('Error changing password', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas zmiany hasła. Spróbuj ponownie.');
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'first_name.required' => 'Imię jest wymagane',
            'last_name.required' => 'Nazwisko jest wymagane',
            'phone.max' => 'Numer telefonu jest za długi',
        ]);

        try {
            $user->update($validated);

            // Jeśli użytkownik jest pacjentem, zaktualizuj też phone w patients
            if ($user->patient) {
                $user->patient->update([
                    'phone' => $validated['phone'],
                ]);
            }

            Log::info('Profile updated', ['user_id' => $user->id]);

            return redirect()->route('settings')
                ->with('success', 'Dane zostały zaktualizowane.');

        } catch (\Exception $e) {
            Log::error('Error updating profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas aktualizacji danych.')
                ->withInput();
        }
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admin;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = User::with(['patient', 'doctor', 'admin']);

        if ($status === 'pending') {
            $query->where('status', User::STATUS_VERIFY);
        } elseif ($status === 'active') {
            $query->where('status', User::STATUS_ACTIVE);
        } elseif ($status === 'inactive') {
            $query->where('status', User::STATUS_INACTIVE);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'all' => User::count(),
            'pending' => User::where('status', User::STATUS_VERIFY)->count(),
            'active' => User::where('status', User::STATUS_ACTIVE)->count(),
            'inactive' => User::where('status', User::STATUS_INACTIVE)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'status'));
    }

    public function show(User $user)
    {
        $user->load(['patient', 'doctor.specializations', 'admin']);

        return view('admin.users.show', compact('user'));
    }

    public function updateStatus(Request $request, User $user)
    {
        try {
            Log::info('Updating user status', [
                'user_id' => $user->id,
                'old_status' => $user->status,
                'new_status' => $request->status
            ]);

            $request->validate([
                'status' => 'required|in:VERIFY,ACTIVE,INACTIVE',
            ]);

            $user->update(['status' => $request->status]);

            Log::info('User status updated successfully', ['user_id' => $user->id]);

            return redirect()->back()->with('success', 'Status użytkownika został zaktualizowany.');
        } catch (\Exception $e) {
            Log::error('Error updating user status', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Błąd: ' . $e->getMessage());
        }
    }

    public function assignRole(Request $request, User $user)
{
    try {
        Log::info('Assigning role to user', [
            'user_id' => $user->id,
            'role' => $request->role,
            'data' => $request->all()
        ]);

        $validated = $request->validate([
            'role' => 'required|in:patient,doctor,admin',
            // PESEL nie jest już wymagany - jest w tabeli users
            'specialization_ids' => 'required_if:role,doctor|nullable|array',
            'specialization_ids.*' => 'exists:specializations,id',
            'position' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        $role = $request->role;

        switch ($role) {
            case 'patient':
                if (!$user->patient) {
                    // Tylko user_id, PESEL jest już w users
                    Patient::create([
                        'user_id' => $user->id,
                        'email' => $user->email,   
                        'phone' => $user->phone, 
                    ]);
                    Log::info('Patient role assigned', ['user_id' => $user->id]);
                }
                break;

            case 'doctor':
                if (!$user->doctor) {
                    $doctor = Doctor::create([
                        'user_id' => $user->id,
                        'name' => $user->full_name,
                    ]);

                    if ($request->specialization_ids) {
                        $doctor->specializations()->attach($request->specialization_ids);
                    }
                    Log::info('Doctor role assigned', ['user_id' => $user->id, 'specializations' => $request->specialization_ids]);
                }
                break;

            case 'admin':
                if (!$user->admin) {
                    Admin::create([
                        'user_id' => $user->id,
                        'position' => $request->position ?? 'Administrator',
                        'permissions' => ['all'],
                    ]);
                    Log::info('Admin role assigned', ['user_id' => $user->id]);
                }
                break;
        }

        if ($user->status === User::STATUS_VERIFY) {
            $user->update(['status' => User::STATUS_ACTIVE]);
            Log::info('User activated after role assignment', ['user_id' => $user->id]);
        }

        DB::commit();

        return redirect()->back()->with('success', "Rola '{$role}' została przypisana użytkownikowi.");
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Validation error in assignRole', [
            'user_id' => $user->id,
            'errors' => $e->errors()
        ]);
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error assigning role to user', [
            'user_id' => $user->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->back()->with('error', 'Wystąpił błąd podczas przypisywania roli: ' . $e->getMessage());
    }
}

    public function editDoctor(User $user)
    {
        if (!$user->doctor) {
            return redirect()->back()->with('error', 'Ten użytkownik nie jest lekarzem.');
        }

        $user->load(['doctor.specializations']);
        $specializations = Specialization::orderBy('name')->get();

        return view('admin.users.edit-doctor', compact('user', 'specializations'));
    }

    public function updateDoctor(Request $request, User $user)
    {
        if (!$user->doctor) {
            return redirect()->back()->with('error', 'Ten użytkownik nie jest lekarzem.');
        }

        try {
            $validated = $request->validate([
                'specialization_ids' => 'required|array|min:1',
                'specialization_ids.*' => 'exists:specializations,id',
            ], [
                'specialization_ids.required' => 'Wybierz co najmniej jedną specjalizację',
                'specialization_ids.min' => 'Wybierz co najmniej jedną specjalizację',
            ]);

            DB::beginTransaction();

            $user->doctor->specializations()->sync($validated['specialization_ids']);

            DB::commit();

            Log::info('Doctor data updated', [
                'user_id' => $user->id,
                'doctor_id' => $user->doctor->id
            ]);

            return redirect()->route('admin.users.show', $user)
                ->with('success', 'Specjalizacje lekarza zostały zaktualizowane.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating doctor data', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas aktualizacji danych: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function changeRole(Request $request, User $user)
{
    try {
        $request->validate([
            'new_role' => 'required|in:patient,doctor,admin',
            'specialization_ids' => 'required_if:new_role,doctor|nullable|array|min:1',
            'specialization_ids.*' => 'exists:specializations,id',
            'position' => 'nullable|string|max:255',
            'confirm_delete_visits' => 'accepted',
        ], [
            'specialization_ids.required_if' => 'Wybierz co najmniej jedną specjalizację',
            'specialization_ids.min' => 'Wybierz co najmniej jedną specjalizację',
            'confirm_delete_visits.accepted' => 'Musisz potwierdzić usunięcie wizyt',
        ]);

        DB::beginTransaction();

        $newRole = $request->new_role;

        $visitsToDelete = 0;

        if ($user->patient) {
            $visitsToDelete += $user->patient->visits()->count();
        }

        if ($user->doctor) {
            $visitsToDelete += $user->doctor->visits()->count();
        }

        if ($user->patient) {
            $user->patient->delete();
        }

        if ($user->doctor) {
            $user->doctor->specializations()->detach();
            $user->doctor->delete();
        }

        if ($user->admin) {
            if (Admin::count() === 1) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Nie można zmienić roli ostatniego administratora w systemie.');
            }
            $user->admin->delete();
        }

        switch ($newRole) {
            case 'patient':
                // Tylko user_id, PESEL jest już w users
                Patient::create([
                    'user_id' => $user->id,
                    'email' => $user->email,    
                    'phone' => $user->phone,   
                ]);
                break;

            case 'doctor':
                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'name' => $user->full_name,
                ]);

                if ($request->specialization_ids) {
                    $doctor->specializations()->attach($request->specialization_ids);
                }
                break;

            case 'admin':
                Admin::create([
                    'user_id' => $user->id,
                    'position' => $request->position ?? 'Administrator',
                    'permissions' => ['all'],
                ]);
                break;
        }

        DB::commit();

        Log::info('User role changed', [
            'user_id' => $user->id,
            'new_role' => $newRole,
            'visits_deleted' => $visitsToDelete
        ]);

        $message = "Rola została zmieniona na '{$newRole}'.";
        if ($visitsToDelete > 0) {
            $message .= " Usunięto {$visitsToDelete} " . ($visitsToDelete === 1 ? 'wizytę' : ($visitsToDelete < 5 ? 'wizyty' : 'wizyt')) . ".";
        }

        return redirect()->route('admin.users.show', $user)->with('success', $message);
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error changing user role', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()->with('error', 'Wystąpił błąd: ' . $e->getMessage());
    }
}

    public function removeRole(User $user)
    {
        try {
            DB::beginTransaction();

            $roleName = '';
            $visitsToDelete = 0;

            if ($user->patient) {
                $visitsToDelete += $user->patient->visits()->count();
                $user->patient->delete();
                $roleName = 'Pacjent';
            }

            if ($user->doctor) {
                $visitsToDelete += $user->doctor->visits()->count();
                $user->doctor->specializations()->detach();
                $user->doctor->delete();
                $roleName = 'Lekarz';
            }

            if ($user->admin) {
                if (Admin::count() === 1) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Nie można usunąć ostatniego administratora w systemie.');
                }

                if ($user->id === auth()->id()) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Nie możesz usunąć swojej własnej roli administratora.');
                }

                $user->admin->delete();
                $roleName = 'Administrator';
            }

            $user->update(['status' => User::STATUS_VERIFY]);

            DB::commit();

            Log::info('User role removed', [
                'user_id' => $user->id,
                'removed_role' => $roleName,
                'visits_deleted' => $visitsToDelete
            ]);

            $message = "Rola '{$roleName}' została usunięta. Użytkownik wymaga ponownej weryfikacji.";
            if ($visitsToDelete > 0) {
                $message .= " Usunięto {$visitsToDelete} " . ($visitsToDelete === 1 ? 'wizytę' : ($visitsToDelete < 5 ? 'wizyty' : 'wizyt')) . ".";
            }

            return redirect()->route('admin.users.show', $user)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing user role', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Wystąpił błąd: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Nie możesz usunąć swojego konta.');
            }

            Log::info('Deleting user', ['user_id' => $user->id]);

            $user->delete();

            Log::info('User deleted successfully', ['user_id' => $user->id]);

            return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
        } catch (\Exception $e) {
            Log::error('Error deleting user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Błąd podczas usuwania użytkownika: ' . $e->getMessage());
        }
    }

    
public function edit(User $user)
{
    return view('admin.users.edit', compact('user'));
}

public function update(Request $request, User $user)
{
    try {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'pesel' => 'nullable|string|size:11|unique:users,pesel,' . $user->id,
        ], [
            'first_name.required' => 'Imię jest wymagane',
            'last_name.required' => 'Nazwisko jest wymagane',
            'email.required' => 'Email jest wymagany',
            'email.email' => 'Podaj poprawny adres email',
            'email.unique' => 'Ten email jest już używany',
            'phone.max' => 'Numer telefonu nie może być dłuższy niż 20 znaków',
            'pesel.size' => 'PESEL musi mieć dokładnie 11 cyfr',
            'pesel.unique' => 'Ten PESEL jest już przypisany do innego użytkownika',
        ]);

        DB::beginTransaction();

        $user->update($validated);

        // Jeśli użytkownik jest lekarzem, zaktualizuj też name w doctors
        if ($user->doctor) {
            $user->doctor->update([
                'name' => $user->full_name
            ]);
        }

        if ($user->patient) {
            $user->patient->update([
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
        }

        DB::commit();

        Log::info('User updated', [
            'user_id' => $user->id,
            'updated_fields' => array_keys($validated)
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Dane użytkownika zostały zaktualizowane.');
            
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating user', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()
            ->with('error', 'Wystąpił błąd podczas aktualizacji: ' . $e->getMessage())
            ->withInput();
    }
}
}
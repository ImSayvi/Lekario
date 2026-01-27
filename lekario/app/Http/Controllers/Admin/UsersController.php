<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = User::with(['patient', 'doctor', 'admin']);
        
        // Filtruj według statusu
        if ($status === 'pending') {
            $query->where('status', User::STATUS_VERIFY);
        } elseif ($status === 'active') {
            $query->where('status', User::STATUS_ACTIVE);
        } elseif ($status === 'inactive') {
            $query->where('status', User::STATUS_INACTIVE);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Statystyki
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
        $user->load(['patient', 'doctor', 'admin']);
        
        return view('admin.users.show', compact('user'));
    }
    
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:VERIFY,ACTIVE,INACTIVE',
        ]);
        
        $user->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Status użytkownika został zaktualizowany.');
    }
    
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:patient,doctor,admin',
            'pesel' => 'required_if:role,patient|nullable|string|size:11|unique:patients,pesel',
            'pwz' => 'required_if:role,doctor|nullable|string|max:50|unique:doctors,pwz',
            'specialization_ids' => 'required_if:role,doctor|nullable|array',
            'position' => 'required_if:role,admin|nullable|string|max:255',
        ]);
        
        DB::beginTransaction();
        
        try {
            $role = $request->role;
            
            // Usuń istniejące role (opcjonalnie - jeśli user może mieć tylko jedną rolę)
            // $user->patient()->delete();
            // $user->doctor()->delete();
            // $user->admin()->delete();
            
            // Przypisz nową rolę
            switch ($role) {
                case 'patient':
                    if (!$user->patient) {
                        Patient::create([
                            'user_id' => $user->id,
                            'pesel' => $request->pesel,
                        ]);
                    }
                    break;
                    
                case 'doctor':
                    if (!$user->doctor) {
                        $doctor = Doctor::create([
                            'user_id' => $user->id,
                            'pwz' => $request->pwz,
                        ]);
                        
                        // Przypisz specjalizacje
                        if ($request->specialization_ids) {
                            $doctor->specializations()->attach($request->specialization_ids);
                        }
                    }
                    break;
                    
                case 'admin':
                    if (!$user->admin) {
                        Admin::create([
                            'user_id' => $user->id,
                            'position' => $request->position ?? 'Administrator',
                            'permissions' => ['all'],
                        ]);
                    }
                    break;
            }
            
            // Aktywuj użytkownika automatycznie po przypisaniu roli
            if ($user->status === User::STATUS_VERIFY) {
                $user->update(['status' => User::STATUS_ACTIVE]);
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', "Rola '{$role}' została przypisana użytkownikowi.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Wystąpił błąd podczas przypisywania roli: ' . $e->getMessage());
        }
    }
    
    public function destroy(User $user)
    {
        // Nie można usunąć siebie
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Nie możesz usunąć swojego konta.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}
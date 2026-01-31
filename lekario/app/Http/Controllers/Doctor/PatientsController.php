<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientsController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $search = $request->get('search');
        
        $query = Patient::whereHas('visits', function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        })->with(['user', 'visits' => function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id)
              ->orderBy('start_time', 'desc');
        }]);
        
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }
        
        $patients = $query->paginate(20);
        
        $stats = [
            'total' => Patient::whereHas('visits', fn($q) => $q->where('doctor_id', $doctor->id))->count(),
            'active' => Patient::whereHas('visits', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id)
                  ->where('start_time', '>=', now()->subMonths(3));
            })->count(),
        ];
        
        return view('doctor.patients.index', compact('patients', 'stats', 'search'));
    }
    
    public function show(Patient $patient)
    {
        $doctor = Auth::user()->doctor;
        
        // Sprawdź czy lekarz miał wizyty z tym pacjentem
        $hasVisits = $patient->visits()->where('doctor_id', $doctor->id)->exists();
        
        if (!$hasVisits) {
            return redirect()->route('doctor.patients.index')
                ->with('error', 'Nie masz dostępu do danych tego pacjenta.');
        }
        
        $patient->load(['user', 'visits' => function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id)
              ->with('doctor.user')
              ->orderBy('start_time', 'desc');
        }]);
        
        // POPRAWKA: Pobieramy bezpośrednio z $patient, a nie z $patient->user
        $prescriptions = $patient->prescriptions()
            ->where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $referrals = $patient->referrals()
            ->where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('doctor.patients.show', compact('patient', 'prescriptions', 'referrals'));
    }
}
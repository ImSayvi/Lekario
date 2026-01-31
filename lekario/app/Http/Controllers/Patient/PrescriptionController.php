<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $patient = $user->patient;

        if (!$patient) {
            return redirect()->route('home')->with('error', 'Nie znaleziono profilu pacjenta');
        }

        // Pobierz wszystkie recepty pacjenta
        $prescriptions = Prescription::whereHas('visit', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with(['visit.doctor.user', 'visit.doctor.specializations'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statystyki
        $stats = [
            'total' => Prescription::whereHas('visit', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })->count(),
            
            'refundable' => Prescription::whereHas('visit', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })->where('is_refundable', true)->count(),
            
            'recent' => Prescription::whereHas('visit', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('patient.prescriptions.index', compact('prescriptions', 'stats'));
    }
}
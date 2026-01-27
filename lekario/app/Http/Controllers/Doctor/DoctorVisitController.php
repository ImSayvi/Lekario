<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorVisitController extends Controller
{
    public function index(Request $request)
    {
        $doctor = auth()->user()->doctor;
        
        // Pobierz parametry filtrów
        $status = $request->get('status', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');
        
        // Buduj zapytanie
        $query = Visit::where('doctor_id', $doctor->id)
            ->with('patient.user')
            ->orderBy('start_time', 'desc');
        
        // Filtr statusu
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // Filtr dat
        if ($dateFrom) {
            $query->whereDate('start_time', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('start_time', '<=', $dateTo);
        }
        
        // Wyszukiwanie po pacjencie
        if ($search) {
            $query->whereHas('patient.user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('pesel', 'like', "%{$search}%");
            });
        }
        
        $visits = $query->paginate(15)->withQueryString();
        
        // Statystyki
        $stats = [
            'all' => Visit::where('doctor_id', $doctor->id)->count(),
            'pending' => Visit::where('doctor_id', $doctor->id)->where('status', 'pending')->count(),
            'accepted' => Visit::where('doctor_id', $doctor->id)->where('status', 'accepted')->count(),
            'completed' => Visit::where('doctor_id', $doctor->id)->where('status', 'completed')->count(),
            'rejected' => Visit::where('doctor_id', $doctor->id)->where('status', 'rejected')->count(),
        ];
        
        return view('doctor.visits.index', compact('visits', 'stats', 'status', 'dateFrom', 'dateTo', 'search'));
    }
    
    public function show(Visit $visit)
    {
        // Sprawdź czy to wizyta tego lekarza
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            abort(403, 'Nie masz uprawnień do tej wizyty.');
        }
        
        $visit->load('patient.user');
        
        return view('doctor.visits.show', compact('visit'));
    }
    
    public function complete(Visit $visit)
    {
        // Sprawdź czy to wizyta tego lekarza
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }
        
        $visit->update(['status' => 'completed']);
        
        return redirect()->back()->with('success', 'Wizyta została oznaczona jako zakończona.');
    }
}
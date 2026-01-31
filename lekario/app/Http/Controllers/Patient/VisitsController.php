<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;

class VisitsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $patient = $user->patient;

        if (!$patient) {
            return redirect()->route('home')->with('error', 'Nie znaleziono profilu pacjenta');
        }

        // Pobierz typ wizyt z parametru (domyślnie: upcoming)
        $type = $request->get('type', 'upcoming');

        $query = Visit::where('patient_id', $patient->id)
            ->with(['doctor.user', 'doctor.specializations']);

        // Filtruj według typu
        switch ($type) {
            case 'upcoming':
                $query->whereIn('status', ['pending', 'accepted'])
                      ->where('start_time', '>', Carbon::now())
                      ->orderBy('start_time', 'asc');
                break;
            
            case 'past':
                $query->where(function($q) {
                    $q->where('status', 'completed')
                      ->orWhere(function($q2) {
                          $q2->whereIn('status', ['pending', 'accepted'])
                             ->where('end_time', '<', Carbon::now());
                      });
                })
                ->orderBy('start_time', 'desc');
                break;
            
            case 'cancelled':
                $query->where('status', 'rejected')
                      ->orderBy('updated_at', 'desc');
                break;
            
            case 'pending':
                $query->where('status', 'pending')
                      ->where('start_time', '>', Carbon::now())
                      ->orderBy('start_time', 'asc');
                break;
            
            default:
                $query->orderBy('start_time', 'desc');
        }

        $visits = $query->paginate(10)->appends(['type' => $type]);

        // Statystyki
        $stats = [
            'upcoming' => Visit::where('patient_id', $patient->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->where('start_time', '>', Carbon::now())
                ->count(),
            
            'pending' => Visit::where('patient_id', $patient->id)
                ->where('status', 'pending')
                ->where('start_time', '>', Carbon::now())
                ->count(),
            
            'completed' => Visit::where('patient_id', $patient->id)
                ->where('status', 'completed')
                ->count(),
            
            'cancelled' => Visit::where('patient_id', $patient->id)
                ->where('status', 'rejected')
                ->count(),
        ];

        return view('patient.visits.index', compact('visits', 'type', 'stats'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $patient = $user->patient;

        $visit = Visit::where('id', $id)
            ->where('patient_id', $patient->id)
            ->with([
                'doctor.user',
                'doctor.specializations',
                'prescriptions',  // Dodane - załaduj recepty
                'referrals'       // Dodane - załaduj skierowania
            ])
            ->firstOrFail();

        return view('patient.visits.show', compact('visit'));
    }

    public function cancel($id)
    {
        $user = auth()->user();
        $patient = $user->patient;

        $visit = Visit::where('id', $id)
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->firstOrFail();

        $visitTime = Carbon::parse($visit->start_time);
        $now = Carbon::now();
        
        // Sprawdź czy wizyta jest w przeszłości
        if ($visitTime->isPast()) {
            return back()->with('error', 'Nie można anulować wizyty, która już się odbyła');
        }

        // Sprawdź czy wizyta jest za mniej niż 24 godziny
        $hoursUntilVisit = $now->diffInHours($visitTime, false);
        
        if ($hoursUntilVisit < 24) {
            return back()->with('error', 'Nie można anulować wizyty na mniej niż 24 godziny przed terminem');
        }

        $visit->status = 'rejected';
        $visit->notes = ($visit->notes ? $visit->notes . "\n\n" : '') 
            . 'Wizyta anulowana przez pacjenta: ' . Carbon::now()->format('Y-m-d H:i:s');
        $visit->save();

        return back()->with('success', 'Wizyta została anulowana');
    }
}
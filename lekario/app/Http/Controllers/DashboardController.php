<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\Referral;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            return redirect()->route('home')->with('error', 'Nie znaleziono profilu pacjenta');
        }

        // Pobierz nadchodzące wizyty (status: pending lub accepted)
        $upcomingVisits = Visit::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->where('start_time', '>', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->with(['doctor.user', 'doctor.specializations'])
            ->get();

        // Najbliższa wizyta
        $nextVisit = $upcomingVisits->first();

        // Statystyki
        $stats = [
            'upcoming_visits' => $upcomingVisits->count(),
            'pending_visits' => Visit::where('patient_id', $patient->id)
                ->where('status', 'pending')
                ->count(),
            'completed_visits' => Visit::where('patient_id', $patient->id)
                ->where('status', 'completed')
                ->count(),
        ];

        // Wizyty w tym miesiącu dla kalendarza
        $currentMonth = Carbon::now()->startOfMonth();
        $monthVisits = Visit::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'accepted', 'completed'])
            ->whereYear('start_time', $currentMonth->year)
            ->whereMonth('start_time', $currentMonth->month)
            ->get()
            ->groupBy(function($visit) {
                return Carbon::parse($visit->start_time)->format('Y-m-d');
            });

        // Wszystkie wizyty dla kalendarza JS (format: ['2024-01-15', '2024-01-20', ...])
        $allVisits = Visit::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'accepted', 'completed'])
            ->get()
            ->pluck('start_time')
            ->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->toArray();

        // Ostatnie recepty (5 najnowszych)
        $recentPrescriptions = Prescription::whereHas('visit', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with('visit')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Ostatnie skierowania (5 najnowszych)
        $recentReferrals = Referral::whereHas('visit', function($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->with('visit')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'upcomingVisits',
            'nextVisit',
            'stats',
            'monthVisits',
            'allVisits',
            'recentPrescriptions',
            'recentReferrals'
        ));
    }

    public function cancelVisit(Request $request, $id)
    {
        $user = Auth::user();
        $patient = $user->patient;

        $visit = Visit::where('id', $id)
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if (!$visit) {
            return back()->with('error', 'Nie można anulować tej wizyty');
        }

        $visitTime = Carbon::parse($visit->start_time);
        $now = Carbon::now();
        
        // Sprawdź czy wizyta jest w przeszłości
        if ($visitTime->isPast()) {
            return back()->with('error', 'Nie można anulować wizyty, która już się odbyła');
        }

        // Sprawdź czy wizyta jest za mniej niż 24 godziny
        // diffInHours(false) - drugi parametr false sprawia, że różnica może być ujemna
        $hoursUntilVisit = $now->diffInHours($visitTime, false);
        
        if ($hoursUntilVisit < 24) {
            return back()->with('error', 'Nie można anulować wizyty na mniej niż 24 godziny przed terminem');
        }

        $visit->status = 'rejected';
        $visit->notes = ($visit->notes ? $visit->notes . "\n" : '') . 'Wizyta anulowana przez pacjenta: ' . Carbon::now()->format('Y-m-d H:i:s');
        $visit->save();

        return back()->with('success', 'Wizyta została anulowana');
    }
}
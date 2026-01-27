<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user()->doctor;
        
        // Wizyty oczekujące na akceptację
        $pendingVisits = Visit::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->where('start_time', '>=', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->with('patient.user')
            ->get();
        
        // Nadchodzące zatwierdzone wizyty
        $upcomingVisits = Visit::where('doctor_id', $doctor->id)
            ->where('status', 'accepted')
            ->where('start_time', '>=', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->with('patient.user')
            ->take(5)
            ->get();
        
        // Statystyki
        $todayVisits = Visit::where('doctor_id', $doctor->id)
            ->whereDate('start_time', Carbon::today())
            ->whereIn('status', ['accepted', 'completed'])
            ->count();
        
        $pendingCount = $pendingVisits->count();
        
        $thisWeekVisits = Visit::where('doctor_id', $doctor->id)
            ->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereIn('status', ['accepted', 'completed'])
            ->count();
        
        return view('doctor.dashboard', compact('upcomingVisits', 'pendingVisits', 'todayVisits', 'pendingCount', 'thisWeekVisits'));
    }

    public function acceptVisit(Request $request, Visit $visit)
    {
        // Sprawdź czy to wizyta tego lekarza
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $visit->update(['status' => 'accepted']);

        return redirect()->back()->with('success', 'Wizyta została zaakceptowana.');
    }

public function updateVisit(Request $request, Visit $visit)
{
    // Sprawdź czy to wizyta tego lekarza
    if ($visit->doctor_id !== auth()->user()->doctor->id) {
        return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
    }

    $request->validate([
        'notes' => 'nullable|string|max:1000',
        'duration' => 'required|integer|min:15|max:120', // 15 min do 2 godzin
    ]);

    $startTime = Carbon::parse($visit->start_time);
    $endTime = $startTime->copy()->addMinutes((int)$request->duration); // Rzutuj na int

    $visit->update([
        'notes' => $request->notes,
        'end_time' => $endTime,
    ]);

    return redirect()->back()->with('success', 'Wizyta została zaktualizowana.');
}

    public function rejectVisit(Request $request, Visit $visit)
    {
        // Sprawdź czy to wizyta tego lekarza
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $visit->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Wizyta została odrzucona.');
    }
}
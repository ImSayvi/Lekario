<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\Referral;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user()->doctor;
        
        // Trwająca wizyta (akceptowana wizyta która się już zaczęła ale jeszcze nie zakończyła)
        $activeVisit = Visit::where('doctor_id', $doctor->id)
            ->where('status', 'accepted')
            ->where('start_time', '<=', Carbon::now())
            ->where('end_time', '>=', Carbon::now())
            ->with('patient.user')
            ->first();
        
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
        
        return view('doctor.dashboard', compact(
            'upcomingVisits', 
            'pendingVisits', 
            'activeVisit',
            'todayVisits', 
            'pendingCount', 
            'thisWeekVisits'
        ));
    }

    public function acceptVisit(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $visit->update(['status' => 'accepted']);

        return redirect()->back()->with('success', 'Wizyta została zaakceptowana.');
    }

    public function updateVisit(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'duration' => 'required|integer|min:15|max:120',
        ]);

        $startTime = Carbon::parse($visit->start_time);
        $endTime = $startTime->copy()->addMinutes((int)$request->duration);

        $visit->update([
            'notes' => $request->notes,
            'end_time' => $endTime,
        ]);

        return redirect()->back()->with('success', 'Wizyta została zaktualizowana.');
    }

    public function rejectVisit(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $visit->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Wizyta została odrzucona.');
    }
    
    public function storePrescription(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $request->validate([
            'medication_name' => 'required|string|max:255',
            'medication_code' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:500',
            'quantity' => 'required|integer|min:1',
            'is_refundable' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        Prescription::create([
            'doctor_id' => auth()->user()->doctor->id,
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
            'medication_name' => $request->medication_name,
            'medication_code' => $request->medication_code,
            'dosage' => $request->dosage,
            'quantity' => $request->quantity,
            'is_refundable' => $request->is_refundable,
            'notes' => $request->notes,
            'issue_date' => Carbon::today(),
            'expiry_date' => Carbon::today()->addDays(30), // ważność 30 dni
        ]);

        return redirect()->back()->with('success', 'Recepta została wystawiona.');
    }
    
    public function storeReferral(Request $request, Visit $visit)
    {
        if ($visit->doctor_id !== auth()->user()->doctor->id) {
            return redirect()->back()->with('error', 'Nie masz uprawnień do tej wizyty.');
        }

        $request->validate([
            'type' => 'required|in:examination,specialist',
            'referral_to' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        Referral::create([
            'doctor_id' => auth()->user()->doctor->id,
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
            'type' => $request->type,
            'referral_to' => $request->referral_to,
            'diagnosis' => $request->diagnosis,
            'notes' => $request->notes,
            'issue_date' => Carbon::today(),
            'valid_until' => Carbon::today()->addMonths(3), // ważność 3 miesiące
        ]);

        return redirect()->back()->with('success', 'Skierowanie zostało wystawione.');
    }
}
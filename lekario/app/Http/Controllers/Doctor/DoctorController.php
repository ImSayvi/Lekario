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
        
        // Pobierz nadchodzące wizyty
        $upcomingVisits = Visit::where('doctor_id', $doctor->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->where('start_time', '>=', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->with('patient.user')
            ->take(5)
            ->get();
        
        // Statystyki
        $todayVisits = Visit::where('doctor_id', $doctor->id)
            ->whereDate('start_time', Carbon::today())
            ->whereIn('status', ['pending', 'accepted'])
            ->count();
        
        $pendingVisits = Visit::where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->count();
        
        $thisWeekVisits = Visit::where('doctor_id', $doctor->id)
            ->whereBetween('start_time', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->whereIn('status', ['accepted', 'completed'])
            ->count();
        
        return view('doctor.dashboard', compact('upcomingVisits', 'todayVisits', 'pendingVisits', 'thisWeekVisits'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Visit;
use App\Models\Prescription;
use App\Models\Referral;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statystyki ogólne
        $stats = [
            'total_users' => User::count(),
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'total_visits' => Visit::count(),
            'pending_visits' => Visit::where('status', 'pending')->count(),
            'completed_visits' => Visit::where('status', 'completed')->count(),
            'total_prescriptions' => Prescription::count(),
            'total_referrals' => Referral::count(),
        ];

        // Ostatnie wizyty
        $recentVisits = Visit::with(['patient.user', 'doctor.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Statystyki wizyt w ostatnich 7 dniach
        $visitsLastWeek = Visit::where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Najaktywniejszi lekarze (według liczby wizyt)
        $topDoctors = Doctor::withCount('visits')
            ->with('user')
            ->orderBy('visits_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentVisits',
            'visitsLastWeek',
            'topDoctors'
        ));
    }
}
<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
{
    $doctor = Auth::user()->doctor;
    
    // Data początkowa (poniedziałek wybranego tygodnia)
    $startOfWeek = $request->has('week') 
        ? Carbon::parse($request->week)->startOfWeek()
        : Carbon::now()->startOfWeek();
    
    $endOfWeek = $startOfWeek->copy()->endOfWeek();
    
    // Pobierz wizyty dla tego tygodnia
    $visits = $doctor->visits()
        ->with(['patient.user'])
        // 1. Zmiana w filtrowaniu bazy
        ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
        // 2. Zmiana w sortowaniu bazy
        ->orderBy('start_time')
        ->get()
        // 3. Zmiana w grupowaniu kolekcji (już w PHP)
        ->groupBy(function ($visit) {
            return $visit->start_time->format('Y-m-d');
        });
    
    // Przygotuj dni tygodnia
    $weekDays = [];
    for ($i = 0; $i < 7; $i++) {
        $date = $startOfWeek->copy()->addDays($i);
        $weekDays[] = [
            'date' => $date,
            'visits' => $visits->get($date->format('Y-m-d'), collect())
        ];
    }
    
    // Nawigacja tygodni (pozostaje bez zmian, bo bazuje na Carbon)
    $previousWeek = $startOfWeek->copy()->subWeek()->format('Y-m-d');
    $nextWeek = $startOfWeek->copy()->addWeek()->format('Y-m-d');
    $currentWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
    
    return view('doctor.schedule.index', compact(
        'weekDays',
        'startOfWeek',
        'endOfWeek',
        'previousWeek',
        'nextWeek',
        'currentWeek'
    ));
}
}
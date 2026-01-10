<?php
namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class VisitController extends Controller
{
    public function create()
    {
        $specializations = Specialization::all();
        return view('visits.create', compact('specializations'));
    }

    /**
     * Pobiera lekarzy dla wybranej specjalizacji (AJAX)
     */
    public function getDoctorsBySpecialization(Request $request)
    {
        $specializationId = $request->specialization_id;
        
        $doctors = Doctor::whereHas('specializations', function($query) use ($specializationId) {
            $query->where('specializations.id', $specializationId);
        })->with('user')->get();

        return response()->json([
            'doctors' => $doctors->map(function($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->full_name,
                ];
            })
        ]);
    }

    /**
     * Pobiera dostępne daty dla wybranych lekarzy (AJAX)
     */
    public function getAvailableDates(Request $request)
    {
        $doctorIds = $request->doctor_ids;
        
        if (empty($doctorIds)) {
            return response()->json(['dates' => []]);
        }

        $doctors = Doctor::whereIn('id', $doctorIds)->get();
        
        // Zbierz wszystkie dostępne daty ze wszystkich wybranych lekarzy
        $allAvailableDates = [];
        foreach ($doctors as $doctor) {
            $dates = $doctor->getAvailableDates();
            $allAvailableDates = array_merge($allAvailableDates, $dates);
        }
        
        // Usuń duplikaty
        $allAvailableDates = array_unique($allAvailableDates);
        sort($allAvailableDates);

        return response()->json(['dates' => $allAvailableDates]);
    }

    /**
     * Pobiera dostępne godziny dla wybranej daty i lekarzy (AJAX)
     */
// Zamień te metody w swoim VisitController.php

public function getAvailableSlots(Request $request)
{
    $doctorIds = $request->doctor_ids;
    $date = $request->date;
    
    $doctors = Doctor::whereIn('id', $doctorIds)->with('user')->get();
    
    $slotsByDoctor = [];
    foreach ($doctors as $doctor) {
        $slots = $doctor->getAvailableSlotsForDate($date);
        
        if (count($slots) > 0) {
            $bookedSlots = Visit::where('doctor_id', $doctor->id)
                ->whereDate('start_time', $date)
                ->whereIn('status', ['pending', 'accepted'])
                ->get()
                ->map(function($visit) {
                    $start = Carbon::parse($visit->start_time)->timezone('Europe/Warsaw');
                    $end = Carbon::parse($visit->end_time)->timezone('Europe/Warsaw');
                    
                    return ['start' => $start, 'end' => $end];
                })
                ->toArray();
            
            $availableSlots = array_filter($slots, function($slot) use ($bookedSlots, $date) {
                $timeSlot = is_array($slot) ? $slot['start'] : $slot;
                $slotStart = Carbon::parse($date . ' ' . $timeSlot, 'Europe/Warsaw');
                $slotEnd = $slotStart->copy()->addMinutes(30);
                
                foreach ($bookedSlots as $bookedSlot) {
                    if ($slotStart < $bookedSlot['end'] && $slotEnd > $bookedSlot['start']) {
                        return false;
                    }
                }
                return true;
            });
            
            $normalizedSlots = array_map(function($slot) {
                return is_array($slot) ? $slot : ['start' => $slot];
            }, array_values($availableSlots));
            
            if (count($normalizedSlots) > 0) {
                $slotsByDoctor[] = [
                    'doctor_id' => $doctor->id,
                    'doctor_name' => $doctor->user->full_name,
                    'slots' => $normalizedSlots,
                ];
            }
        }
    }

    return response()->json(['slots' => $slotsByDoctor]);
}

public function store(Request $request)
{
    $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'date' => 'required|date|after_or_equal:today',
        'time_slot' => 'required',
    ]);

    $user = auth()->user();
    $patient = $user->patient;

    if (!$patient) {
        return redirect()->back()->with('error', 'Nie jesteś zarejestrowanym pacjentem.');
    }

    // Sprawdź ile rezerwacji zostało UTWORZONYCH dzisiaj
    $todayCreatedVisitsCount = Visit::where('patient_id', $patient->id)
        ->whereDate('created_at', Carbon::today())
        ->count();

    if ($todayCreatedVisitsCount >= 3) {
        return redirect()->back()->with('error', 'Osiągnąłeś dzienny limit 3 rezerwacji.');
    }

    // Sprawdź czy pacjent ma już oczekującą lub zaakceptowaną wizytę u tego lekarza w przyszłości
    $hasPendingVisitWithDoctor = Visit::where('patient_id', $patient->id)
        ->where('doctor_id', $request->doctor_id)
        ->whereIn('status', ['pending', 'accepted'])
        ->where('start_time', '>=', Carbon::now())
        ->exists();

    if ($hasPendingVisitWithDoctor) {
        return redirect()->back()->with('error', 'Masz już zaplanowaną wizytę u tego lekarza. Poczekaj na jej realizację lub anulowanie przed umówieniem kolejnej.');
    }

    $dateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time_slot, 'Europe/Warsaw');
    $endTime = $dateTime->copy()->addMinutes(30);

    $isBooked = Visit::where('doctor_id', $request->doctor_id)
        ->whereIn('status', ['pending', 'accepted'])
        ->where(function($query) use ($dateTime, $endTime) {
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $dateTime);
        })
        ->exists();

    if ($isBooked) {
        return redirect()->back()->with('error', 'Ten termin został już zajęty. Wybierz inny.');
    }

    Visit::create([
        'doctor_id' => $request->doctor_id,
        'patient_id' => $patient->id,
        'start_time' => $dateTime,
        'end_time' => $endTime,
        'status' => 'pending',
    ]);

    return redirect()->route('visits.create')->with('success', 'Wizyta została umówiona! Oczekuje na potwierdzenie.');
}
    
}
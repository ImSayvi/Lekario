<?php
namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
public function getAvailableSlots(Request $request)
{
    $doctorIds = $request->doctor_ids;
    $date = $request->date;
    
    $doctors = Doctor::whereIn('id', $doctorIds)->with('user')->get();
    
    $slotsByDoctor = [];
    foreach ($doctors as $doctor) {
        $slots = $doctor->getAvailableSlotsForDate($date);
        
        if (count($slots) > 0) {
            // Pobierz zajęte sloty dla tego lekarza w tym dniu
            $bookedSlots = Visit::where('doctor_id', $doctor->id)
                ->whereDate('start_time', $date)
                ->whereIn('status', ['pending', 'accepted'])
                ->get()
                ->map(function($visit) {
                    return Carbon::parse($visit->start_time)->format('H:i');
                })
                ->toArray();
            
            // Filtruj i zwróć TYLKO dostępne sloty
            $availableSlots = array_filter($slots, function($slot) use ($bookedSlots) {
                // Obsługa zarówno array ['start' => '08:00'] jak i string '08:00'
                $timeSlot = is_array($slot) ? $slot['start'] : $slot;
                return !in_array($timeSlot, $bookedSlots);
            });
            
            // Normalizuj format slotów do ['start' => 'XX:XX']
            $normalizedSlots = array_map(function($slot) {
                if (is_array($slot)) {
                    return $slot;
                }
                return ['start' => $slot];
            }, array_values($availableSlots));
            
            // Jeśli są dostępne sloty, dodaj do wyników
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

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $patient = $user->patient;

        if (!$patient) {
            return redirect()->back()->with('error', 'Nie jesteś zarejestrowanym pacjentem.');
        }

        // Parsuj slot czasowy (format: "08:00")
        $dateTime = Carbon::parse($request->date . ' ' . $request->time_slot);
        $endTime = $dateTime->copy()->addMinutes(30);

        // Sprawdź czy slot jest nadal dostępny
        $isBooked = Visit::where('doctor_id', $request->doctor_id)
            ->whereDate('start_time', $request->date)
            ->whereIn('status', ['pending', 'accepted'])
            ->where(function($query) use ($dateTime, $endTime) {
                $query->whereBetween('start_time', [$dateTime, $endTime])
                      ->orWhereBetween('end_time', [$dateTime, $endTime])
                      ->orWhere(function($q) use ($dateTime, $endTime) {
                          $q->where('start_time', '<=', $dateTime)
                            ->where('end_time', '>=', $endTime);
                      });
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
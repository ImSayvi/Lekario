<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Doctor extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'doctor_specialization');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

public function getAvailableSlotsForDate($date)
{
    // Parsuj datę w strefie czasowej Europe/Warsaw
    $date = Carbon::parse($date, 'Europe/Warsaw')->startOfDay();
    $slots = [];
    
    // Godziny pracy: 8:00 - 15:00 (7 godzin)
    $startHour = 8;
    $endHour = 15;
    
    // Pobierz zajęte wizyty w tym dniu (w lokalnej strefie czasowej)
    $bookedVisits = $this->visits()
        ->whereDate('start_time', $date->format('Y-m-d'))
        ->whereIn('status', ['pending', 'accepted'])
        ->get();
    
    // Generuj wszystkie możliwe sloty (co 30 min)
    for ($hour = $startHour; $hour < $endHour; $hour++) {
        foreach ([0, 30] as $minute) {
            $slotStart = $date->copy()->setTime($hour, $minute, 0);
            $slotEnd = $slotStart->copy()->addMinutes(30);
            
            // Sprawdź czy slot jest wolny
            $isBooked = $bookedVisits->contains(function ($visit) use ($slotStart, $slotEnd) {
                $visitStart = Carbon::parse($visit->start_time);
                $visitEnd = Carbon::parse($visit->end_time);
                
                // Slot jest zajęty tylko jeśli NAKŁADA SIĘ na wizytę
                // Slot 8:30-9:00 NIE nakłada się na wizytę 8:00-8:30
                return ($slotStart < $visitEnd && $slotEnd > $visitStart);
            });
            
            if (!$isBooked) {
                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'datetime' => $slotStart,
                ];
            }
        }
    }
    
    return $slots;
}

public function getAvailableDates($daysAhead = 30)
{
    $availableDates = [];
    // Użyj Carbon::now() zamiast Carbon::today() dla lepszej kontroli strefy czasowej
    $today = Carbon::now('Europe/Warsaw')->startOfDay();
    
    for ($i = 0; $i < $daysAhead; $i++) {
        $date = $today->copy()->addDays($i);
        
        // Pomijamy weekendy (sobota = 6, niedziela = 0)
        if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
            continue;
        }
        
        // Sprawdź czy są dostępne sloty
        $slots = $this->getAvailableSlotsForDate($date);
        
        if (count($slots) > 0) {
            $availableDates[] = $date->format('Y-m-d');
        }
    }
    
    return $availableDates;
}
}
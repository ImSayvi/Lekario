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

    /**
     * Sprawdza dostępność lekarza w danym dniu
     */
    public function getAvailableSlotsForDate($date)
    {
        $date = Carbon::parse($date);
        $slots = [];
        
        // Godziny pracy: 8:00 - 15:00 (7 godzin)
        $startHour = 8;
        $endHour = 15;
        
        // Pobierz zajęte wizyty w tym dniu
        $bookedVisits = $this->visits()
            ->whereDate('start_time', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
        
        // Generuj wszystkie możliwe sloty (co 30 min)
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            foreach ([0, 30] as $minute) {
                $slotStart = Carbon::parse($date->format('Y-m-d'))->setTime($hour, $minute);
                $slotEnd = $slotStart->copy()->addMinutes(30);
                
                // Sprawdź czy slot jest wolny
                $isBooked = $bookedVisits->contains(function ($visit) use ($slotStart, $slotEnd) {
                    $visitStart = Carbon::parse($visit->start_time);
                    $visitEnd = Carbon::parse($visit->end_time);
                    
                    return $slotStart->between($visitStart, $visitEnd, false) ||
                           $slotEnd->between($visitStart, $visitEnd, false) ||
                           ($slotStart <= $visitStart && $slotEnd >= $visitEnd);
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

    /**
     * Pobiera dostępne dni dla lekarza (następne 30 dni)
     */
    public function getAvailableDates($daysAhead = 30)
    {
        $availableDates = [];
        $today = Carbon::today();
        
        for ($i = 0; $i < $daysAhead; $i++) {
            $date = $today->copy()->addDays($i);
            
            // Pomijamy weekendy
            if ($date->isWeekend()) {
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
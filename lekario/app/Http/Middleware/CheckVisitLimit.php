<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckVisitLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user && $user->patient) {
            // Sprawdź ile rezerwacji zostało UTWORZONYCH dzisiaj
            $todayCreatedVisitsCount = Visit::where('patient_id', $user->patient->id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            if ($todayCreatedVisitsCount >= 3) {
                return redirect()->route('dashboard')
                    ->with('error', 'Osiągnąłeś dzienny limit 3 rezerwacji. Spróbuj ponownie jutro.');
            }
        }

        return $next($request);
    }
}
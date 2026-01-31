<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $patient = $user->patient;

        if (!$patient) {
            return redirect()->route('home')->with('error', 'Nie znaleziono profilu pacjenta');
        }

        // Pobierz typ z parametru (domyślnie: all)
        $type = $request->get('type', 'all');

        // Query builder
        $query = Referral::whereHas('visit', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })
            ->with(['visit.doctor.user', 'visit.doctor.specializations']);

        // Filtruj według typu
        if ($type === 'examination') {
            $query->where('type', 'examination');
        } elseif ($type === 'specialist') {
            $query->where('type', 'specialist');
        }

        $referrals = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statystyki
        $stats = [
            'all' => Referral::whereHas('visit', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->count(),
            
            'examination' => Referral::whereHas('visit', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->where('type', 'examination')->count(),
            
            'specialist' => Referral::whereHas('visit', function($q) use ($patient) {
                $q->where('patient_id', $patient->id);
            })->where('type', 'specialist')->count(),
        ];

        return view('patient.referrals.index', compact('referrals', 'stats', 'type'));
    }
}
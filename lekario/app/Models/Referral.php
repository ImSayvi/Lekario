<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'visit_id',
        'type',
        'referral_to',
        'diagnosis',
        'notes',
        'issue_date',
        'valid_until',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
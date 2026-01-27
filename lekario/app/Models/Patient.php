<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['user_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function visits() {
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
}

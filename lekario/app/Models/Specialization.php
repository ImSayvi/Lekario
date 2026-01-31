<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model {
    protected $fillable = ['name'];
    public $timestamps = false;
    public function doctors() {
        return $this->belongsToMany(Doctor::class);
    }
}

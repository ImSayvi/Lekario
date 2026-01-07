<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\VisitController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//tu se wrzucam te do których dostępu nie mają niezalogowani
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    
    // AJAX endpoints
    Route::post('/api/visits/doctors-by-specialization', [VisitController::class, 'getDoctorsBySpecialization']);
    Route::post('/api/visits/available-dates', [VisitController::class, 'getAvailableDates']);
    Route::post('/api/visits/available-slots', [VisitController::class, 'getAvailableSlots']);

});


require __DIR__.'/auth.php';

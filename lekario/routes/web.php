<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\Doctor\DoctorDashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Przekierowanie na odpowiedni dashboard w zależności od roli
Route::get('/dashboard', function () {
    if (auth()->check() && auth()->user()->doctor) {
        return redirect()->route('doctor.dashboard');
    }
    // Jeśli pacjent, użyj kontrolera
    if (auth()->check() && auth()->user()->patient) {
        return app(DashboardController::class)->index();
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Trasy dla pacjentów
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Trasy z limitem wizyt
    Route::middleware('check.visit.limit')->group(function () {
        Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
    });
    
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    Route::post('/visits/{id}/cancel', [DashboardController::class, 'cancelVisit'])->name('visits.cancel');
    
    // Placeholder routes dla innych sekcji menu (możesz je później rozwinąć)
    Route::get('/appointments', function() { 
        return view('appointments.index'); 
    })->name('appointments.index');
    
    Route::get('/medical-records', function() { 
        return view('medical-records.index'); 
    })->name('medical-records.index');
    
    Route::get('/test-results', function() { 
        return view('test-results.index'); 
    })->name('test-results.index');
    
    Route::get('/prescriptions', function() { 
        return view('prescriptions.index'); 
    })->name('prescriptions.index');
    
    Route::get('/messages', function() { 
        return view('messages.index'); 
    })->name('messages.index');
    
    Route::get('/settings', function() { 
        return view('settings.index'); 
    })->name('settings');
    
    // AJAX endpoints
    Route::post('/api/visits/doctors-by-specialization', [VisitController::class, 'getDoctorsBySpecialization']);
    Route::post('/api/visits/available-dates', [VisitController::class, 'getAvailableDates']);
    Route::post('/api/visits/available-slots', [VisitController::class, 'getAvailableSlots']);
});

// Trasy dla lekarzy
Route::middleware(['auth', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/visits/{visit}/accept', [DoctorDashboardController::class, 'acceptVisit'])->name('visits.accept');
    Route::post('/visits/{visit}/reject', [DoctorDashboardController::class, 'rejectVisit'])->name('visits.reject');
    Route::patch('/visits/{visit}/update', [DoctorDashboardController::class, 'updateVisit'])->name('visits.update');
    
    // Nowe trasy dla modułu wizyt
    Route::get('/visits', [App\Http\Controllers\Doctor\DoctorVisitController::class, 'index'])->name('visits.index');
    Route::get('/visits/{visit}', [App\Http\Controllers\Doctor\DoctorVisitController::class, 'show'])->name('visits.show');
    Route::post('/visits/{visit}/complete', [App\Http\Controllers\Doctor\DoctorVisitController::class, 'complete'])->name('visits.complete');
    
    Route::get('/schedule', function() { return 'Harmonogram'; })->name('schedule');
});

require __DIR__.'/auth.php';
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Patient\VisitsController as PatientVisitsController;
use App\Http\Controllers\Patient\ReferralController as PatientReferralController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
// use App\Http\Controllers\Doctor\DoctorVisitController;



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
    if (auth()->check() && auth()->user()->admin) {
        return redirect()->route('admin.dashboard');
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
    
    // Moduł "Moje Wizyty" dla pacjenta
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/visits', [PatientVisitsController::class, 'index'])->name('visits.index');
        Route::get('/visits/{id}', [PatientVisitsController::class, 'show'])->name('visits.show');
        Route::delete('/visits/{id}', [PatientVisitsController::class, 'cancel'])->name('visits.cancel');
    });
    
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

    Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
    
    // Skierowania
    Route::get('/referrals', [PatientReferralController::class, 'index'])->name('referrals.index');
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
    
    Route::post('/visits/{visit}/prescription', [DoctorDashboardController::class, 'storePrescription'])->name('visits.prescription.store');
    Route::post('/visits/{visit}/referral', [DoctorDashboardController::class, 'storeReferral'])->name('visits.referral.store');

     // Recepty
    Route::post('/visits/{visit}/prescription', [DoctorVisitController::class, 'storePrescription'])
        ->name('visits.prescription.store');
    Route::put('/prescriptions/{prescription}', [DoctorVisitController::class, 'updatePrescription'])
        ->name('prescriptions.update');
    Route::delete('/prescriptions/{prescription}', [DoctorVisitController::class, 'destroyPrescription'])
        ->name('prescriptions.destroy');
    
    // Skierowania
    Route::post('/visits/{visit}/referral', [DoctorVisitController::class, 'storeReferral'])
        ->name('visits.referral.store');
    Route::put('/referrals/{referral}', [DoctorVisitController::class, 'updateReferral'])
        ->name('referrals.update');
    Route::delete('/referrals/{referral}', [DoctorVisitController::class, 'destroyReferral'])
        ->name('referrals.destroy');
    
    Route::get('/schedule', function() { return 'Harmonogram'; })->name('schedule');
});

//trasy admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUsersController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [AdminUsersController::class, 'updateStatus'])->name('users.update-status');
    Route::post('/users/{user}/assign-role', [AdminUsersController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
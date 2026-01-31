<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
<<<<<<< HEAD
use App\Http\Controllers\Patient\VisitsController as PatientVisitsController;
use App\Http\Controllers\Patient\ReferralController as PatientReferralController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Doctor\DoctorVisitController;
use App\Http\Controllers\Admin\SpecializationsController;
use App\Http\Controllers\Patient\SettingsController;
use App\Http\Controllers\Doctor\SettingsController as DoctorSettingsController;
use App\Http\Controllers\Doctor\PatientsController as DoctorPatientsController;
use App\Http\Controllers\Doctor\ScheduleController as DoctorScheduleController;



=======
>>>>>>> 39a24c01deb4d86612cb0a74fe9c0e44d98d2cbd

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
<<<<<<< HEAD

    Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
    
    // Skierowania
    Route::get('/referrals', [PatientReferralController::class, 'index'])->name('referrals.index');

     Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    
=======
>>>>>>> 39a24c01deb4d86612cb0a74fe9c0e44d98d2cbd
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
    
<<<<<<< HEAD
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
    
    //pacjenci
    Route::get('/patients', [DoctorPatientsController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [DoctorPatientsController::class, 'show'])->name('patients.show');
    
    // Harmonogram - NOWE
    Route::get('/schedule', [DoctorScheduleController::class, 'index'])->name('schedule');
    

        Route::get('/settings', [DoctorSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/password', [DoctorSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::patch('/settings/profile', [DoctorSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    
});

//trasy admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Użytkownicy
    Route::get('/users', [AdminUsersController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUsersController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUsersController::class, 'edit'])->name('users.edit'); // NOWE
    Route::patch('/users/{user}', [AdminUsersController::class, 'update'])->name('users.update'); // NOWE
    Route::patch('/users/{user}/status', [AdminUsersController::class, 'updateStatus'])->name('users.update-status');
    Route::post('/users/{user}/assign-role', [AdminUsersController::class, 'assignRole'])->name('users.assign-role');
    Route::delete('/users/{user}', [AdminUsersController::class, 'destroy'])->name('users.destroy');
    
    // Edycja lekarza 
    Route::get('/users/{user}/edit-doctor', [AdminUsersController::class, 'editDoctor'])->name('users.edit-doctor');
    Route::patch('/users/{user}/update-doctor', [AdminUsersController::class, 'updateDoctor'])->name('users.update-doctor');
    
    // Zmiana i usuwanie roli
    Route::patch('/users/{user}/change-role', [AdminUsersController::class, 'changeRole'])->name('users.change-role.update');
    Route::delete('/users/{user}/remove-role', [AdminUsersController::class, 'removeRole'])->name('users.remove-role');
    
    // Specjalizacje
    Route::get('/specializations', [SpecializationsController::class, 'index'])->name('specializations.index');
    Route::get('/specializations/create', [SpecializationsController::class, 'create'])->name('specializations.create');
    Route::post('/specializations', [SpecializationsController::class, 'store'])->name('specializations.store');
    Route::get('/specializations/{specialization}/edit', [SpecializationsController::class, 'edit'])->name('specializations.edit');
    Route::patch('/specializations/{specialization}', [SpecializationsController::class, 'update'])->name('specializations.update');
    Route::delete('/specializations/{specialization}', [SpecializationsController::class, 'destroy'])->name('specializations.destroy');
});

=======
    Route::get('/schedule', function() { return 'Harmonogram'; })->name('schedule');
});

>>>>>>> 39a24c01deb4d86612cb0a74fe9c0e44d98d2cbd
require __DIR__.'/auth.php';
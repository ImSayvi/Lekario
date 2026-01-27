# Oddzielne Dashboardy dla Pacjenta i Lekarza

System posiada oddzielne dashboardy dla różnych ról użytkowników (pacjent, lekarz, admin).

## Struktura Route'ów

### Routes (web.php)

```php
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;

// Dashboard dla pacjentów
Route::middleware(['auth'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/visits', [PatientDashboardController::class, 'visits'])->name('visits');
});

// Dashboard dla lekarzy
Route::middleware(['auth'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/visits', [DoctorDashboardController::class, 'visits'])->name('visits');
    Route::get('/schedule', [DoctorDashboardController::class, 'schedule'])->name('schedule');
});
```

## Struktura Kontrolerów

### Patient/DashboardController.php

```php
<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $patient = $user->patient;
        
        // Pobierz dane specyficzne dla pacjenta
        $upcomingVisits = $patient->visits()
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();
            
        return view('patient.dashboard', compact('upcomingVisits'));
    }
    
    public function visits()
    {
        $patient = auth()->user()->patient;
        $visits = $patient->visits()->with('doctor.user')->paginate(10);
        
        return view('patient.visits', compact('visits'));
    }
}
```

### Doctor/DashboardController.php

```php
<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        
        // Pobierz dane specyficzne dla lekarza
        $todayVisits = $doctor->visits()
            ->whereDate('start_time', today())
            ->with('patient.user')
            ->orderBy('start_time')
            ->get();
            
        return view('doctor.dashboard', compact('todayVisits'));
    }
    
    public function visits()
    {
        $doctor = auth()->user()->doctor;
        $visits = $doctor->visits()->with('patient.user')->paginate(10);
        
        return view('doctor.visits', compact('visits'));
    }
}
```

## Przekierowanie po Logowaniu

W `app/Http/Controllers/Auth/LoginController.php` lub `AuthenticatedSessionController.php`:

```php
protected function authenticated(Request $request, $user)
{
    // Przekieruj użytkownika na odpowiedni dashboard w zależności od roli
    if ($user->doctor) {
        return redirect()->route('doctor.dashboard');
    }
    
    if ($user->patient) {
        return redirect()->route('patient.dashboard');
    }
    
    if ($user->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    
    return redirect('/');
}
```

## Struktura Widoków

```
resources/views/
├── patient/
│   ├── dashboard.blade.php
│   ├── visits.blade.php
│   └── medical-records.blade.php
├── doctor/
│   ├── dashboard.blade.php
│   ├── visits.blade.php
│   ├── schedule.blade.php
│   └── patients.blade.php

```

## Zabezpieczenia

### Sprawdzanie roli w kontrolerze

```php
public function index()
{
    $user = auth()->user();
    
    // Sprawdź czy użytkownik ma odpowiednią rolę
    if (!$user->patient) {
        abort(403, 'Dostęp tylko dla pacjentów');
    }
    
    // ... reszta kodu
}
```

### Middleware (opcjonalnie)

Można stworzyć dedykowane middleware dla każdej roli:

```bash
php artisan make:middleware EnsureUserIsPatient
php artisan make:middleware EnsureUserIsDoctor
```

```php
// app/Http/Middleware/EnsureUserIsPatient.php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->patient) {
        abort(403, 'Dostęp tylko dla pacjentów');
    }
    
    return $next($request);
}
```

Rejestracja w `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    'patient' => \App\Http\Middleware\EnsureUserIsPatient::class,
    'doctor' => \App\Http\Middleware\EnsureUserIsDoctor::class,
];
```

Użycie w routes:

```php
Route::middleware(['auth', 'patient'])->prefix('patient')->group(function () {
    // routes
});
```

## Nawigacja w Blade

W layoutach można warunkowo pokazywać linki w zależności od roli:

```blade
@if(auth()->user()->patient)
    <a href="{{ route('patient.dashboard') }}">Mój Dashboard</a>
    <a href="{{ route('patient.visits') }}">Moje Wizyty</a>
@elseif(auth()->user()->doctor)
    <a href="{{ route('doctor.dashboard') }}">Panel Lekarza</a>
    <a href="{{ route('doctor.visits') }}">Wizyty</a>
    <a href="{{ route('doctor.schedule') }}">Harmonogram</a>
@endif
```

## Podsumowanie

System rozróżnia użytkowników po relacjach:
- `$user->patient` - relacja do modelu Patient
- `$user->doctor` - relacja do modelu Doctor


Każda rola ma:
- ✅ Własny prefix URL (`/patient/...`, `/doctor/...`)
- ✅ Własny namespace kontrolerów
- ✅ Własne widoki
- ✅ Dedykowane funkcjonalności
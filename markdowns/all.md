# Dokumentacja Projektu Lekario - Stan Obecny

## Wersja Laravel
**Laravel 12** (bez `Kernel.php`, scheduler w `routes/console.php` lub `bootstrap/app.php`)

## Konfiguracja Strefy Czasowej
```php
// config/app.php
'timezone' => 'Europe/Warsaw',

// .env
APP_TIMEZONE=Europe/Warsaw
```

---

## Struktura Bazy Danych

### Tabele

#### users
- id
- first_name
- last_name
- email
- password
- created_at
- updated_at

#### patients
- id
- user_id (FK -> users)
- created_at
- updated_at

#### doctors
- id
- user_id (FK -> users)
- created_at
- updated_at

#### specializations
- id
- name
- created_at
- updated_at

#### doctor_specialization (pivot)
- doctor_id (FK -> doctors)
- specialization_id (FK -> specializations)

#### visits
- id
- doctor_id (FK -> doctors)
- patient_id (FK -> patients)
- start_time (datetime)
- end_time (datetime)
- status (enum: 'pending', 'accepted', 'completed', 'rejected')
- notes (text, nullable)
- created_at
- updated_at

### Relacje w Modelach

**User.php**
```php
public function patient() // hasOne
public function doctor() // hasOne
```

**Patient.php**
```php
public function user() // belongsTo
public function visits() // hasMany
```

**Doctor.php**
```php
public function user() // belongsTo
public function specializations() // belongsToMany
public function visits() // hasMany
```

**Visit.php**
```php
protected $fillable = ['doctor_id', 'patient_id', 'start_time', 'end_time', 'status', 'notes'];
protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];

public function doctor() // belongsTo
public function patient() // belongsTo
```

---

## Struktura Kontrolerów

### 1. DashboardController
**Lokalizacja:** `app/Http/Controllers/DashboardController.php`

**Metody:**
- `index()` - Dashboard pacjenta, pokazuje nadchodzące wizyty, statystyki, kalendarz
- `cancelVisit($id)` - Anulowanie wizyty (wymaga >24h przed terminem)

---

### 2. VisitController
**Lokalizacja:** `app/Http/Controllers/VisitController.php`

**Metody:**
- `create()` - Formularz umawiania wizyty (4-krokowy proces)
- `getDoctorsBySpecialization(Request)` - AJAX: pobiera lekarzy dla specjalizacji
- `getAvailableDates(Request)` - AJAX: pobiera dostępne daty dla lekarzy
- `getAvailableSlots(Request)` - AJAX: pobiera dostępne sloty czasowe (filtruje zajęte)
- `store(Request)` - Zapisuje nową wizytę z walidacją:
  - Limit 3 rezerwacji dziennie
  - Sprawdza czy pacjent ma już wizytę u tego lekarza
  - Sprawdza nakładanie się terminów

**Ważne funkcje:**
- Używa `Carbon::createFromFormat()` z `'Europe/Warsaw'`
- Filtruje sloty używając logiki: `$slotStart < $visitEnd && $slotEnd > $visitStart`
- Wizyty zapisywane są w lokalnej strefie czasowej

---

### 3. Patient/VisitsController
**Lokalizacja:** `app/Http/Controllers/Patient/VisitsController.php`

**Metody:**
- `index(Request)` - Lista wizyt pacjenta z filtrami (upcoming, past, cancelled, pending)
- `show($id)` - Szczegóły pojedynczej wizyty
- `cancel($id)` - Anulowanie wizyty (wymaga >24h przed terminem)

**Funkcje:**
- 4 typy widoków: nadchodzące, oczekujące, historia, anulowane
- Statystyki dla każdej kategorii
- Paginacja (10 wizyt na stronę)

---

### 4. Doctor/DoctorDashboardController
**Lokalizacja:** `app/Http/Controllers/Doctor/DoctorDashboardController.php`

**Metody:**
- `index()` - Dashboard lekarza z wizytami na dziś
- `acceptVisit($visit)` - Akceptacja wizyty
- `rejectVisit($visit)` - Odrzucenie wizyty
- `updateVisit(Request, $visit)` - Aktualizacja notatek wizyty

---

### 5. Doctor/DoctorVisitController
**Lokalizacja:** `app/Http/Controllers/Doctor/DoctorVisitController.php`

**Metody:**
- `index()` - Lista wizyt lekarza
- `show($visit)` - Szczegóły wizyty
- `complete($visit)` - Oznaczenie wizyty jako ukończonej

---

## Widoki (Views)

### Layouts
**`resources/views/layouts/app.blade.php`**
- Sidebar z nawigacją
- Header z tytułem strony
- Obsługa sesji (success/error messages)
- Licznik wizyt (X/3)

### Patient Views

**Dashboard:**
- `resources/views/dashboard.blade.php` - główny dashboard pacjenta

**Wizyty - Umawianie:**
- `resources/views/visits/create.blade.php` - 4-krokowy formularz:
  1. Wybór specjalizacji
  2. Wybór lekarza/lekarzy (checkbox)
  3. Kalendarz z dostępnymi datami
  4. Wybór godziny (sloty 30-minutowe)

**Wizyty - Przeglądanie:**
- `resources/views/patient/visits/index.blade.php` - lista wizyt z filtrami i statystykami
- `resources/views/patient/visits/show.blade.php` - szczegóły wizyty

### Doctor Views
**Struktura:**
```
resources/views/doctor/
├── dashboard.blade.php
├── visits/
│   ├── index.blade.php
│   └── show.blade.php
```

---

## Routes

### Pacjent (`routes/web.php`)

```php
// Dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->doctor) {
        return redirect()->route('doctor.dashboard');
    }
    if (auth()->user()->patient) {
        return app(DashboardController::class)->index();
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Umawianie wizyt
Route::middleware('auth')->group(function () {
    Route::middleware('check.visit.limit')->group(function () {
        Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
    });
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    
    // Moduł "Moje Wizyty"
    Route::prefix('patient')->name('patient.')->group(function () {
        Route::get('/visits', [PatientVisitsController::class, 'index'])->name('visits.index');
        Route::get('/visits/{id}', [PatientVisitsController::class, 'show'])->name('visits.show');
        Route::delete('/visits/{id}', [PatientVisitsController::class, 'cancel'])->name('visits.cancel');
    });
    
    // AJAX endpoints
    Route::post('/api/visits/doctors-by-specialization', [VisitController::class, 'getDoctorsBySpecialization']);
    Route::post('/api/visits/available-dates', [VisitController::class, 'getAvailableDates']);
    Route::post('/api/visits/available-slots', [VisitController::class, 'getAvailableSlots']);
});
```

### Lekarz

```php
Route::middleware(['auth', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/visits/{visit}/accept', [DoctorDashboardController::class, 'acceptVisit'])->name('visits.accept');
    Route::post('/visits/{visit}/reject', [DoctorDashboardController::class, 'rejectVisit'])->name('visits.reject');
    Route::patch('/visits/{visit}/update', [DoctorDashboardController::class, 'updateVisit'])->name('visits.update');
    
    Route::get('/visits', [DoctorVisitController::class, 'index'])->name('visits.index');
    Route::get('/visits/{visit}', [DoctorVisitController::class, 'show'])->name('visits.show');
    Route::post('/visits/{visit}/complete', [DoctorVisitController::class, 'complete'])->name('visits.complete');
});
```

---

## Middleware

### CheckVisitLimit
**Lokalizacja:** `app/Http/Middleware/CheckVisitLimit.php`

**Funkcja:** Sprawdza czy pacjent nie przekroczył limitu 3 rezerwacji dziennie

**Użycie:**
```php
Route::middleware('check.visit.limit')->group(function () {
    Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
});
```

### CheckDoctor
**Lokalizacja:** `app/Http/Middleware/CheckDoctor.php`

**Funkcja:** Sprawdza czy użytkownik jest lekarzem

**Rejestracja w `bootstrap/app.php`:**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'doctor' => \App\Http\Middleware\CheckDoctor::class,
    ]);
})
```

---

## Automatyczne Ukończanie Wizyt

### Command
**Lokalizacja:** `app/Console/Commands/CompleteExpiredVisits.php`

**Sygnatura:** `visits:complete-expired`

**Funkcja:** 
- Znajduje wizyty ze statusem 'pending' lub 'accepted' których `end_time` minął
- Zmienia status na 'completed'
- Dodaje notatkę: "Wizyta się nie odbyła - automatycznie oznaczona jako ukończona"

### Scheduler
**Lokalizacja:** `routes/console.php`

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('visits:complete-expired')
    ->hourly()
    ->withoutOverlapping();
```

### Cron (na serwerze produkcyjnym)
```cron
* * * * * cd /ścieżka/do/projektu && php artisan schedule:run >> /dev/null 2>&1
```

### Testowanie
```bash
# Ręcznie
php artisan visits:complete-expired

# Scheduler lokalnie
php artisan schedule:run
php artisan schedule:work
```

---

## Logika Biznesowa

### Umawianie Wizyty

**Walidacje:**
1. ✅ Limit 3 rezerwacji dziennie (sprawdzane po `created_at`)
2. ✅ Pacjent może mieć tylko 1 oczekującą/zaakceptowaną wizytę u danego lekarza
3. ✅ Sprawdzanie nakładania się terminów (sloty 30-minutowe)
4. ✅ Data wizyty nie może być w przeszłości

**Proces:**
1. Wybór specjalizacji → AJAX pobiera lekarzy
2. Wybór lekarza/lekarzy → AJAX pobiera dostępne daty
3. Wybór daty → AJAX pobiera dostępne godziny (filtrowane)
4. Wybór godziny → Submit formularza

### Anulowanie Wizyty

**Warunki:**
- Status: 'pending' lub 'accepted'
- Czas do wizyty: >= 24 godziny
- Wizyta w przyszłości

**Proces:**
1. Zmiana statusu na 'rejected'
2. Dodanie notatki z datą i czasem anulowania

### Dostępne Sloty

**Filtrowanie zajętych:**
```php
// Slot jest zajęty jeśli:
$slotStart < $visitEnd && $slotEnd > $visitStart
```

**Przykład:**
- Wizyta: 8:00-8:30
- Slot 8:30-9:00: NIE nakłada się (dostępny) ✅
- Slot 8:00-8:30: nakłada się (zajęty) ❌
- Slot 8:15-8:45: nakłada się (zajęty) ❌

### Godziny Pracy Lekarza

**Model Doctor.php metoda `getAvailableSlotsForDate()`:**
- Godziny: 8:00 - 15:00
- Sloty: co 30 minut
- Pomija weekendy
- Generuje sloty na 30 dni naprzód

---

## Kluczowe Pliki do Sprawdzenia

### Kontrolery
```
app/Http/Controllers/
├── DashboardController.php
├── VisitController.php
├── Patient/
│   └── VisitsController.php
└── Doctor/
    ├── DoctorDashboardController.php
    └── DoctorVisitController.php
```

### Modele
```
app/Models/
├── User.php
├── Patient.php
├── Doctor.php
├── Visit.php
└── Specialization.php
```

### Middleware
```
app/Http/Middleware/
├── CheckVisitLimit.php
└── CheckDoctor.php
```

### Commands
```
app/Console/Commands/
└── CompleteExpiredVisits.php
```

### Widoki
```
resources/views/
├── layouts/
│   └── app.blade.php
├── dashboard.blade.php
├── visits/
│   └── create.blade.php
├── patient/
│   └── visits/
│       ├── index.blade.php
│       └── show.blade.php
└── doctor/
    ├── dashboard.blade.php
    └── visits/
        ├── index.blade.php
        └── show.blade.php
```

---

## Funkcjonalności Zaimplementowane

### Pacjent
- ✅ Dashboard z nadchodzącymi wizytami i kalendarzem
- ✅ Umawianie wizyt (4-krokowy proces)
- ✅ Lista wizyt z filtrami (nadchodzące, oczekujące, historia, anulowane)
- ✅ Szczegóły wizyty
- ✅ Anulowanie wizyty (>24h przed terminem)
- ✅ Limit 3 rezerwacji dziennie
- ✅ Jedna wizyta u lekarza na raz

### Lekarz
- ✅ Dashboard z wizytami na dziś
- ✅ Akceptacja/odrzucenie wizyty
- ✅ Lista wszystkich wizyt
- ✅ Szczegóły wizyty
- ✅ Dodawanie notatek do wizyty
- ✅ Oznaczanie wizyty jako ukończonej

### System
- ✅ Automatyczne ukończanie przeterminowanych wizyt (cron)
- ✅ Obsługa stref czasowych (Europe/Warsaw)
- ✅ Filtrowanie zajętych slotów
- ✅ Walidacja nakładających się terminów
- ✅ Responsywny design (Tailwind CSS)

---

## Funkcjonalności Do Zrobienia (Placeholders)

W `layouts/app.blade.php` są linki do:
- Dokumentacja medyczna
- Wyniki badań
- Recepty
- Wiadomości
- Ustawienia

Te sekcje mają placeholder routes i wymagają implementacji.

---

## Znane Problemy i Rozwiązania

### Problem: Wizyty zapisywały się z błędną datą/godziną
**Rozwiązanie:** Używanie `Carbon::createFromFormat()` z jawną strefą czasową `'Europe/Warsaw'`

### Problem: Slot 8:30 był blokowany przez wizytę 8:00-8:30
**Rozwiązanie:** Zmiana logiki z `between()` na `$slotStart < $visitEnd && $slotEnd > $visitStart`

### Problem: Weekendy pokazywały się jako dostępne
**Rozwiązanie:** Sprawdzanie `$date->dayOfWeek === 0 || $date->dayOfWeek === 6` w `getAvailableDates()`

### Problem: Kalendarz pokazywał błędne daty
**Rozwiązanie:** Używanie lokalnego formatowania zamiast `toISOString()` w JavaScript

---

## Notatki Techniczne

### Frontend
- Tailwind CSS (CDN)
- Vanilla JavaScript (AJAX)
- Brak localStorage (nie wspierany w artefaktach)

### Backend
- Laravel 12
- Carbon dla dat/czasu
- Eloquent ORM
- PostgreSQL/MySQL (kompatybilne)

### Bezpieczeństwo
- CSRF protection
- Middleware auth
- Walidacja wszystkich inputów
- Sprawdzanie własności zasobów (pacjent może zobaczyć tylko swoje wizyty)

---

## Jak Wrócić do Projektu

1. **Sprawdź konfigurację:**
   - `config/app.php` - timezone
   - `.env` - APP_TIMEZONE

2. **Sprawdź czy działa scheduler:**
   ```bash
   php artisan schedule:run
   php artisan visits:complete-expired
   ```

3. **Sprawdź routes:**
   ```bash
   php artisan route:list
   ```

4. **Sprawdź middleware:**
   ```bash
   php artisan route:list --middleware=check.visit.limit
   php artisan route:list --middleware=doctor
   ```

5. **Testuj kluczowe flow:**
   - Umawianie wizyty
   - Anulowanie wizyty
   - Dashboard pacjenta
   - Dashboard lekarza

---

## Kontakt i Wsparcie

Jeśli masz pytania lub napotkasz problemy:
1. Sprawdź logi: `storage/logs/laravel.log`
2. Sprawdź bazę danych (szczególnie tabele `visits` i `users`)
3. Użyj `dd()` lub `Log::info()` do debugowania

---

**Data ostatniej aktualizacji:** 2026-01-27  
**Wersja dokumentacji:** 1.0
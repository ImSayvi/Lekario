<div align="center">

<img src=" https://capsule-render.vercel.app/api?type=waving&height=300&color=gradient&text=🩺%20Lekario&reversal=true&descAlign=50 " width="100%"/>

### Nowoczesny System Zarządzania Przychodnią

<p>
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"/>
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/Tailwind-4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind"/>
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js"/>
</p>

<p>
  <img src="https://img.shields.io/badge/License-MIT-10b981?style=flat-square" alt="License"/>
  <img src="https://img.shields.io/badge/Status-Active-brightgreen?style=flat-square" alt="Status"/>
</p>

**Kompleksowa platforma medyczna łącząca pacjentów z lekarzami**

[Funkcje](#-funkcje) • [Instalacja](#-instalacja) • [Technologie](#-technologie) • [API](#-api)

</div>

---

## O Projekcie

**Lekario** to aplikacja webowa do zarządzania przychodnią medyczną. System oferuje intuicyjny interfejs zarówno dla pacjentów, jak i lekarzy.

### Dlaczego Lekario?

- ⏰ **Oszczędność czasu** - rezerwacja wizyty w 30 sekund
- 🔐 **Bezpieczeństwo** - zgodność z RODO
- 📱 **Responsywność** - działa na każdym urządzeniu
- 🇵🇱 **Polski interfejs** - pełna lokalizacja

---

## Funkcje

### Dla Pacjentów

| Funkcja | Opis |
|---------|------|
|  **Rezerwacja online** | Umów wizytę 24/7 bez dzwonienia |
|  **Wybór specjalisty** | Przeglądaj lekarzy według specjalizacji |
|  **Interaktywny kalendarz** | Zobacz dostępne terminy |
|  **Anulowanie wizyt** | Odwołaj wizytę jednym kliknięciem |
|  **Historia wizyt** | Dostęp do dokumentacji medycznej |
|  **Powiadomienia** | Przypomnienia o wizytach |

### Dla Lekarzy

| Funkcja | Opis |
|---------|------|
|  **Panel lekarza** | Dedykowany dashboard ze statystykami |
|  **Akceptacja wizyt** | Zarządzaj rezerwacjami pacjentów |
|  **Edycja czasu** | Dostosuj czas trwania wizyty (15-120 min) |
|  **Notatki** | Zapisuj uwagi do wizyt |
|  **Filtry i wyszukiwanie** | Znajdź pacjentów po PESEL, nazwisku |
|  **Status wizyt** | Oznaczaj wizyty jako zakończone |

---

## Instalacja

### Wymagania

- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL / SQLite

### Szybki start

\`\`\`bash
# Sklonuj repozytorium
git clone https://github.com/aimek/Lekario.git
cd Lekario/lekario

# Automatyczna instalacja
composer setup

# Zależności
composer install
npm install

# Konfiguracja
cp .env.example .env
php artisan key:generate

# Baza danych
touch database/database.sqlite  # lub skonfiguruj MySQL w .env
php artisan migrate

# Build
npm run build

# Serwer deweloperski
composer dev

# LUB
npm run start

Aplikacja: **http://localhost:8000**

---

## Struktura Projektu

\`\`\`
lekario/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php      # Panel pacjenta
│   │   │   ├── VisitController.php          # Rezerwacja wizyt
│   │   │   └── Doctor/                      # Kontrolery lekarza
│   │   └── Middleware/
│   │       ├── CheckVisitLimit.php          # Limit 3 wizyt/dzień
│   │       └── EnsureUserIsDoctor.php       # Autoryzacja lekarza
│   └── Models/
│       ├── User.php
│       ├── Doctor.php
│       ├── Patient.php
│       ├── Visit.php
│       └── Specialization.php
├── resources/views/
│   ├── welcome.blade.php                    # Strona główna
│   ├── dashboard.blade.php                  # Dashboard pacjenta
│   ├── doctor/                              # Panel lekarza
│   └── visits/                              # Rezerwacja wizyt
├── routes/
│   ├── web.php                              # Routing główny
│   └── auth.php                             # Autoryzacja
└── database/migrations/                     # Migracje
\`\`\`

---

## Baza Danych

### Główne tabele

| Tabela | Opis |
|--------|------|
| \`users\` | Użytkownicy (imię, nazwisko, email, PESEL, telefon) |
| \`doctors\` | Lekarze (powiązani z users) |
| \`patients\` | Pacjenci (powiązani z users) |
| \`visits\` | Wizyty (lekarz, pacjent, czas, status, notatki) |
| \`specializations\` | Specjalizacje lekarzy |
| \`doctor_specialization\` | Relacja lekarz-specjalizacja |

### Statusy wizyt

| Status | Opis |
|--------|------|
| \`pending\` | 🟡 Oczekuje na akceptację |
| \`accepted\` | 🟢 Zaakceptowana |
| \`completed\` | 🔵 Zakończona |
| \`rejected\` | 🔴 Odrzucona/Anulowana |






---

## 🎨 Technologie

**Backend:** Laravel 12, PHP 8.2, Eloquent ORM, Laravel Breeze

**Frontend:** Blade Templates, Tailwind CSS 4.0, Alpine.js 3.x, Vite 7.x

**Narzędzia:** Pest (testy), Laravel Pint (linting), Carbon (daty)

---



## 📄 Licencja

MIT License © 2026 Lekario

---

<div align="center">

Made with 💚 in Poland

<img src="https://capsule-render.vercel.app/api?type=waving&color=0:10b981,100:059669&height=120&section=footer" width="100%"/>

</div>

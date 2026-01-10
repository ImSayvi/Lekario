@extends('layouts.app')

@section('title', 'Umów wizytę')
@section('page-title', 'Umów wizytę')
@section('page-subtitle', 'Wybierz specjalizację, lekarza, datę i godzinę')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
        <form id="visitForm" action="{{ route('visits.store') }}" method="POST">
            @csrf

            <!-- Krok 1: Wybór specjalizacji -->
            <div id="step1" class="step-section">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">1. Wybierz specjalizację</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($specializations as $spec)
                        <label class="specialization-card cursor-pointer">
                            <input type="radio" name="specialization_id" value="{{ $spec->id }}" class="hidden specialization-radio">
                            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-emerald-500 transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="font-medium text-gray-700">{{ $spec->name }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Krok 2: Wybór lekarzy -->
            <div id="step2" class="step-section hidden mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">2. Wybierz lekarza/lekarzy</h3>
                <div id="doctorsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Lekarze będą załadowani przez AJAX -->
                </div>
            </div>

            <!-- Krok 3: Kalendarz -->
            <div id="step3" class="step-section hidden mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">3. Wybierz datę</h3>
                <div id="calendar" class="bg-gray-50 p-4 rounded-lg">
                    <!-- Kalendarz będzie wygenerowany przez JavaScript -->
                </div>
            </div>

            <!-- Krok 4: Wybór godziny -->
            <div id="step4" class="step-section hidden mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">4. Wybierz godzinę</h3>
                <input type="hidden" name="doctor_id" id="selectedDoctorId">
                <input type="hidden" name="date" id="selectedDate">
                <input type="hidden" name="time_slot" id="selectedTimeSlot">
                
                <div id="slotsContainer">
                    <!-- Sloty czasowe będą załadowane przez AJAX -->
                </div>

                <button type="submit" id="submitBtn" class="mt-6 w-full bg-emerald-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-emerald-700 transition hidden">
                    Potwierdź rezerwację
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .specialization-card input:checked + div {
        border-color: #059669;
        background-color: #f0fdf4;
    }
    
    .doctor-card input:checked + div {
        border-color: #059669;
        background-color: #f0fdf4;
    }

    .calendar-day {
        padding: 0.75rem;
        text-align: center;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .calendar-day.available {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 500;
    }

    .calendar-day.available:hover {
        background-color: #a7f3d0;
    }

    .calendar-day.selected {
        background-color: #059669;
        color: white;
    }

    .calendar-day.unavailable {
        background-color: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .time-slot {
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
    }

    .time-slot:hover {
        border-color: #059669;
        background-color: #f0fdf4;
    }

    .time-slot.selected {
        border-color: #059669;
        background-color: #059669;
        color: white;
    }

    .loader {
        border: 3px solid #f3f4f6;
        border-top: 3px solid #059669;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-text {
        text-align: center;
        color: #6b7280;
        margin-top: 10px;
        font-size: 14px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedDoctors = [];
    let availableDates = [];
    let selectedDate = null;

    // Obsługa wyboru specjalizacji
    document.querySelectorAll('.specialization-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                selectedDoctors = [];
                document.getElementById('step3').classList.add('hidden');
                document.getElementById('step4').classList.add('hidden');
                loadDoctors(this.value);
            }
        });
    });

    // Załaduj lekarzy dla specjalizacji
    function loadDoctors(specializationId) {
        const container = document.getElementById('doctorsContainer');
        container.innerHTML = '<div class="col-span-full"><div class="loader"></div><div class="loading-text">Ładowanie lekarzy...</div></div>';
        
        fetch('/api/visits/doctors-by-specialization', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ specialization_id: specializationId })
        })
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';
            selectedDoctors = [];

            if (data.doctors.length === 0) {
                container.innerHTML = '<p class="text-gray-500">Brak dostępnych lekarzy dla tej specjalizacji.</p>';
                return;
            }

            data.doctors.forEach(doctor => {
                container.innerHTML += `
                    <label class="doctor-card cursor-pointer">
                        <input type="checkbox" value="${doctor.id}" class="hidden doctor-checkbox">
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-emerald-500 transition">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700">${doctor.name}</span>
                            </div>
                        </div>
                    </label>
                `;
            });

            document.getElementById('step2').classList.remove('hidden');

            // Obsługa wyboru lekarzy
            document.querySelectorAll('.doctor-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedDoctors.push(this.value);
                    } else {
                        selectedDoctors = selectedDoctors.filter(id => id !== this.value);
                    }

                    if (selectedDoctors.length > 0) {
                        loadAvailableDates();
                    } else {
                        document.getElementById('step3').classList.add('hidden');
                        document.getElementById('step4').classList.add('hidden');
                    }
                });
            });
        });
    }

    // Załaduj dostępne daty
    function loadAvailableDates() {
        const calendar = document.getElementById('calendar');
        calendar.innerHTML = '<div class="loader"></div><div class="loading-text">Ładowanie dostępnych terminów...</div>';
        document.getElementById('step3').classList.remove('hidden');
        
        fetch('/api/visits/available-dates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ doctor_ids: selectedDoctors })
        })
        .then(response => response.json())
        .then(data => {
            availableDates = data.dates;
            renderCalendar();
        });
    }

    // Renderuj kalendarz
    function renderCalendar() {
        const calendar = document.getElementById('calendar');
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Ustaw na początek dnia
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();

        // Dodaj nagłówek z nazwą miesiąca
        const monthNames = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 
                           'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];
        
        let html = `<div class="text-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-700">${monthNames[currentMonth]} ${currentYear}</h4>
                    </div>`;
        
        html += '<div class="grid grid-cols-7 gap-2">';
        
        // Nagłówki dni tygodnia
        const days = ['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So', 'Nd'];
        days.forEach(day => {
            html += `<div class="text-center font-semibold text-gray-600 py-2">${day}</div>`;
        });

        // Oblicz pierwszy dzień miesiąca
        const firstDay = new Date(currentYear, currentMonth, 1);
        let dayOfWeek = firstDay.getDay();
        dayOfWeek = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

        // Dodaj puste komórki przed pierwszym dniem
        for (let i = 0; i < dayOfWeek; i++) {
            html += '<div></div>';
        }

        // Dodaj dni miesiąca
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(currentYear, currentMonth, day);
            date.setHours(0, 0, 0, 0);
            
            // Format daty YYYY-MM-DD w lokalnej strefie czasowej
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const dayStr = String(date.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${dayStr}`;
            
            const isAvailable = availableDates.includes(dateStr);
            const isPast = date < today;

            let classes = 'calendar-day ';
            if (isPast) {
                classes += 'unavailable';
            } else if (isAvailable) {
                classes += 'available';
            } else {
                classes += 'unavailable';
            }

            html += `<div class="${classes}" data-date="${dateStr}" onclick="selectDate('${dateStr}', ${isAvailable && !isPast})">${day}</div>`;
        }

        html += '</div>';
        calendar.innerHTML = html;
    }

    // Wybierz datę
    window.selectDate = function(date, isAvailable) {
        if (!isAvailable) return;

        selectedDate = date;
        
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.classList.remove('selected');
        });

        event.target.classList.add('selected');
        loadAvailableSlots(date);
    };

    // Załaduj dostępne sloty czasowe
    function loadAvailableSlots(date) {
        document.getElementById('selectedDate').value = date;
        
        const container = document.getElementById('slotsContainer');
        container.innerHTML = '<div class="loader"></div><div class="loading-text">Ładowanie dostępnych godzin...</div>';
        document.getElementById('step4').classList.remove('hidden');

        fetch('/api/visits/available-slots', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                doctor_ids: selectedDoctors,
                date: date 
            })
        })
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';

            if (data.slots.length === 0) {
                container.innerHTML = '<p class="text-gray-500">Brak dostępnych terminów w tym dniu.</p>';
                return;
            }

            data.slots.forEach(doctorSlots => {
                let html = `
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">${doctorSlots.doctor_name}</h4>
                        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                `;

                doctorSlots.slots.forEach(slot => {
                    html += `
                        <div class="time-slot" onclick="selectTimeSlot('${doctorSlots.doctor_id}', '${slot.start}')">
                            ${slot.start}
                        </div>
                    `;
                });

                html += '</div></div>';
                container.innerHTML += html;
            });
        });
    }

    // Wybierz slot czasowy
    window.selectTimeSlot = function(doctorId, time) {
        document.getElementById('selectedDoctorId').value = doctorId;
        document.getElementById('selectedTimeSlot').value = time;

        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });

        event.target.classList.add('selected');
        document.getElementById('submitBtn').classList.remove('hidden');
    };
});
</script>
@endsection
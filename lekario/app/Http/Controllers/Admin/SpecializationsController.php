<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SpecializationsController extends Controller
{
    public function index()
    {
        $specializations = Specialization::withCount('doctors')
            ->orderBy('name')
            ->paginate(20);
        
        return view('admin.specializations.index', compact('specializations'));
    }
    
    public function create()
    {
        return view('admin.specializations.create');
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:specializations,name',
            ], [
                'name.required' => 'Nazwa specjalizacji jest wymagana',
                'name.unique' => 'Specjalizacja o tej nazwie już istnieje',
                'name.max' => 'Nazwa nie może być dłuższa niż 255 znaków',
            ]);
            
            $specialization = Specialization::create($validated);
            
            Log::info('Specialization created', [
                'specialization_id' => $specialization->id,
                'name' => $specialization->name
            ]);
            
            return redirect()->route('admin.specializations.index')
                ->with('success', 'Specjalizacja została dodana.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating specialization', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas dodawania specjalizacji: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function edit(Specialization $specialization)
    {
        return view('admin.specializations.edit', compact('specialization'));
    }
    
    public function update(Request $request, Specialization $specialization)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:specializations,name,' . $specialization->id,
            ], [
                'name.required' => 'Nazwa specjalizacji jest wymagana',
                'name.unique' => 'Specjalizacja o tej nazwie już istnieje',
                'name.max' => 'Nazwa nie może być dłuższa niż 255 znaków',
                'description.max' => 'Opis nie może być dłuższy niż 1000 znaków',
            ]);
            
            $specialization->update($validated);
            
            Log::info('Specialization updated', [
                'specialization_id' => $specialization->id,
                'name' => $specialization->name
            ]);
            
            return redirect()->route('admin.specializations.index')
                ->with('success', 'Specjalizacja została zaktualizowana.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating specialization', [
                'specialization_id' => $specialization->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas aktualizacji specjalizacji: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function destroy(Specialization $specialization)
    {
        try {
            // Sprawdź czy są lekarze z tą specjalizacją
            $doctorsCount = $specialization->doctors()->count();
            
            if ($doctorsCount > 0) {
                return redirect()->back()
                    ->with('error', "Nie można usunąć specjalizacji, która jest przypisana do {$doctorsCount} lekarzy. Najpierw usuń ją z profili lekarzy.");
            }
            
            $name = $specialization->name;
            $specialization->delete();
            
            Log::info('Specialization deleted', [
                'specialization_id' => $specialization->id,
                'name' => $name
            ]);
            
            return redirect()->route('admin.specializations.index')
                ->with('success', 'Specjalizacja została usunięta.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting specialization', [
                'specialization_id' => $specialization->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Wystąpił błąd podczas usuwania specjalizacji: ' . $e->getMessage());
        }
    }
}
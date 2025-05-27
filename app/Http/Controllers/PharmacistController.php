<?php

namespace App\Http\Controllers;

use App\Models\Pharmacist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmacistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pharmacists.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pharmacists.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:pharmacists',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'license_number' => 'required|string|max:50|unique:pharmacists',
            'specialization' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Pharmacist::create($request->all());

        return redirect()->route('pharmacists.index')
            ->with('success', 'Pharmacien créé avec succès.');
    }

    /**
     * Affiche les détails d'un pharmacien spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $pharmacist = Pharmacist::with('sales')->findOrFail($id);
        return view('pharmacists.show', compact('pharmacist'));
    }

    /**
     * Affiche le formulaire de modification d'un pharmacien.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $pharmacist = Pharmacist::findOrFail($id);
        return view('pharmacists.edit', compact('pharmacist'));
    }

    /**
     * Met à jour un pharmacien spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $pharmacist = Pharmacist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:pharmacists,email,' . $pharmacist->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'license_number' => 'required|string|max:50|unique:pharmacists,license_number,' . $pharmacist->id,
            'specialization' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date',
        ]);

        $pharmacist->update($validated);

        return redirect()->route('pharmacists.index')
            ->with('success', 'Pharmacien mis à jour avec succès.');
    }

    /**
     * Supprime un pharmacien spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $pharmacist = Pharmacist::findOrFail($id);
        
        // Vérifier si le pharmacien a des ventes associées
        if ($pharmacist->sales()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer ce pharmacien car il a des ventes associées.']);
        }
        
        $pharmacist->delete();

        return redirect()->route('pharmacists.index')
            ->with('success', 'Pharmacien supprimé avec succès.');
    }
}

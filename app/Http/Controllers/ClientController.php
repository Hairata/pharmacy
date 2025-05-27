<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clients.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'health_insurance_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Client::create($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Affiche les détails d'un client spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $client = Client::with('sales')->findOrFail($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Affiche le formulaire de modification d'un client.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Met à jour un client spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'health_insurance_number' => 'nullable|string|max:50',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprime un client spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        
        // Vérifier si le client a des ventes associées
        if ($client->sales()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer ce client car il a des ventes associées.']);
        }
        
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}

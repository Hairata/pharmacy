<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Affiche le formulaire de création d'un produit.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Enregistre un nouveau produit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'category' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // Gérer l'upload d'image si présent
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affiche les détails d'un produit spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Affiche le formulaire de modification d'un produit.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Met à jour un produit spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'category' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        // Gérer l'upload d'image si présent
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        
        // Supprimer l'image associée si elle existe
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}

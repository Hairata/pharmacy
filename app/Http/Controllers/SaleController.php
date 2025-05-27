<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\Pharmacist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Affiche la liste des ventes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('sales.index');
    }

    /**
     * Affiche le formulaire de création d'une vente.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $clients = Client::all();
        $pharmacists = Pharmacist::all();
        $products = Product::where('stock_quantity', '>', 0)->get();
        
        return view('sales.create', compact('clients', 'pharmacists', 'products'));
    }

    /**
     * Enregistre une nouvelle vente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'pharmacist_id' => 'required|exists:pharmacists,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,card,insurance',
            'status' => 'required|string|in:completed,pending,cancelled',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Calculer le montant total et vérifier les stocks
        $totalAmount = 0;
        $productsData = [];
        
        foreach ($validated['products'] as $item) {
            $product = Product::findOrFail($item['id']);
            
            // Vérifier si le stock est suffisant
            if ($product->stock_quantity < $item['quantity']) {
                return back()->withErrors([
                    'products' => "Stock insuffisant pour {$product->name}. Disponible: {$product->stock_quantity}"
                ])->withInput();
            }
            
            $itemPrice = $product->price * $item['quantity'];
            $totalAmount += $itemPrice;
            
            $productsData[$item['id']] = [
                'quantity' => $item['quantity'],
                'price' => $product->price
            ];
        }

        DB::beginTransaction();
        
        try {
            // Créer la vente
            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'pharmacist_id' => $validated['pharmacist_id'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Attacher les produits à la vente
            $sale->products()->attach($productsData);
            
            // Mettre à jour les stocks
            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['id']);
                $product->decrement('stock_quantity', $item['quantity']);
            }
            
            DB::commit();
            
            return redirect()->route('sales.index')
                ->with('success', 'Vente créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la création de la vente.'])->withInput();
        }
    }

    /**
     * Affiche les détails d'une vente spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function show(string $id)
    {
        $sale = Sale::with(['client', 'pharmacist', 'products'])->findOrFail($id);
        return view('sales.show', compact('sale'));
    }

    /**
     * Affiche le formulaire de modification d'une vente.
     *
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function edit(string $id)
    {
        $sale = Sale::with('products')->findOrFail($id);
        $clients = Client::all();
        $pharmacists = Pharmacist::all();
        $products = Product::all();
        
        return view('sales.edit', compact('sale', 'clients', 'pharmacists', 'products'));
    }

    /**
     * Met à jour une vente spécifique.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        $sale = Sale::with('products')->findOrFail($id);

        dump($sale);
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'pharmacist_id' => 'required|exists:pharmacists,id',
            'sale_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,card,insurance',
            'status' => 'required|string|in:completed,pending,cancelled',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        
        try {
            // Restaurer les stocks des produits de la vente actuelle
            foreach ($sale->products as $product) {
                $product->increment('stock_quantity', $product->pivot->quantity);
            }
            
            // Calculer le nouveau montant total et vérifier les stocks
            $totalAmount = 0;
            $productsData = [];
            
            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['id']);
                
                // Vérifier si le stock est suffisant
                if ($product->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return back()->withErrors([
                        'products' => "Stock insuffisant pour {$product->name}. Disponible: {$product->stock_quantity}"
                    ])->withInput();
                }
                
                $itemPrice = $product->price * $item['quantity'];
                $totalAmount += $itemPrice;
                
                $productsData[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ];
                
                // Décrémenter le stock
                $product->decrement('stock_quantity', $item['quantity']);
            }
            
            // Mettre à jour la vente
            $sale->update([
                'client_id' => $validated['client_id'],
                'pharmacist_id' => $validated['pharmacist_id'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Synchroniser les produits
            $sale->products()->sync($productsData);
            
            DB::commit();
            
            return redirect()->route('sales.index')
                ->with('success', 'Vente mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour de la vente.'])->withInput();
        }
    }

    /**
     * Supprime une vente spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $sale = Sale::with('products')->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Restaurer les stocks des produits si la vente était complétée
            if ($sale->status === 'completed') {
                foreach ($sale->products as $product) {
                    $product->increment('stock_quantity', $product->pivot->quantity);
                }
            }
            
            // Supprimer la vente (les relations seront supprimées automatiquement grâce aux contraintes de clé étrangère)
            $sale->delete();
            
            DB::commit();
            
            return redirect()->route('sales.index')
                ->with('success', 'Vente supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la suppression de la vente.']);
        }
    }
    
    /**
     * Affiche les statistiques des ventes.
     *
     * @return \Illuminate\View\View
     */
    public function stats()
    {
        return view('sales.stats');
    }
}

<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Client;
use App\Models\Product;
use App\Models\Pharmacist;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SaleForm extends Component
{
    public $saleId;
    public $client_id;
    public $pharmacist_id;
    public $sale_date;
    public $payment_method = 'cash';
    public $status = 'completed';
    public $notes;
    public $selectedProducts = [];
    public $isEditing = false;
    
    protected $rules = [
        'client_id' => 'required|exists:clients,id',
        'pharmacist_id' => 'required|exists:pharmacists,id',
        'sale_date' => 'required|date',
        'payment_method' => 'required|string|in:cash,card,insurance',
        'status' => 'required|string|in:completed,pending,cancelled',
        'notes' => 'nullable|string',
        'selectedProducts' => 'required|array|min:1',
        'selectedProducts.*.product_id' => 'required|exists:products,id',
        'selectedProducts.*.quantity' => 'required|integer|min:1',
    ];
    
    public function mount($saleId = null)
    {
        $this->sale_date = Carbon::now()->format('Y-m-d');
        
        if ($saleId) {
            $this->saleId = $saleId;
            $this->isEditing = true;
            $this->loadSale();
        } else {
            // Initialiser avec un produit vide
            $this->selectedProducts = [
                ['product_id' => '', 'quantity' => 1, 'price' => 0, 'subtotal' => 0]
            ];
        }
    }
    
    public function loadSale()
    {
        $sale = Sale::with('products')->findOrFail($this->saleId);
        
        $this->client_id = $sale->client_id;
        $this->pharmacist_id = $sale->pharmacist_id;
        $this->sale_date = $sale->sale_date->format('Y-m-d');
        $this->payment_method = $sale->payment_method;
        $this->status = $sale->status;
        $this->notes = $sale->notes;
        
        $this->selectedProducts = [];
        foreach ($sale->products as $product) {
            $this->selectedProducts[] = [
                'product_id' => $product->id,
                'quantity' => $product->pivot->quantity,
                'price' => $product->price,
                'subtotal' => $product->pivot->quantity * $product->price,
                'name' => $product->name,
                'stock' => $product->stock_quantity + $product->pivot->quantity // Ajouter la quantité de la vente au stock actuel pour l'édition
            ];
        }
    }
    
    public function updatedSelectedProducts($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 3 && $parts[2] === 'product_id' && !empty($value)) {
            $index = $parts[1];
            $product = Product::find($value);
            
            if ($product) {
                $this->selectedProducts[$index]['price'] = $product->price;
                $this->selectedProducts[$index]['name'] = $product->name;
                $this->selectedProducts[$index]['stock'] = $product->stock_quantity;
                $this->calculateSubtotal($index);
            }
        } elseif (count($parts) === 3 && $parts[2] === 'quantity') {
            $index = $parts[1];
            $this->calculateSubtotal($index);
        }
    }
    
    public function calculateSubtotal($index)
    {
        if (isset($this->selectedProducts[$index]['quantity']) && isset($this->selectedProducts[$index]['price'])) {
            $quantity = $this->selectedProducts[$index]['quantity'];
            $price = $this->selectedProducts[$index]['price'];
            $this->selectedProducts[$index]['subtotal'] = $quantity * $price;
        }
    }
    
    public function addProduct()
    {
        $this->selectedProducts[] = ['product_id' => '', 'quantity' => 1, 'price' => 0, 'subtotal' => 0];
    }
    
    public function removeProduct($index)
    {
        if (count($this->selectedProducts) > 1) {
            unset($this->selectedProducts[$index]);
            $this->selectedProducts = array_values($this->selectedProducts);
        }
    }
    
    public function getTotalProperty()
    {
        $total = 0;
        foreach ($this->selectedProducts as $item) {
            $total += isset($item['subtotal']) ? $item['subtotal'] : 0;
        }
        return $total;
    }
    
    public function save()
    {
        $this->validate();
        
        // Transformer les données pour le traitement
        $products = [];
        foreach ($this->selectedProducts as $item) {
            $products[] = [
                'id' => $item['product_id'],
                'quantity' => $item['quantity']
            ];
        }
        
        DB::beginTransaction();
        
        try {
            // Calculer le montant total et vérifier les stocks
            $totalAmount = 0;
            $productsData = [];
            
            foreach ($products as $item) {
                $product = Product::findOrFail($item['id']);
                
                // Si on édite, on doit ajuster le stock disponible
                $availableStock = $product->stock_quantity;
                if ($this->isEditing) {
                    $sale = Sale::with('products')->findOrFail($this->saleId);
                    $existingProduct = $sale->products->firstWhere('id', $product->id);
                    if ($existingProduct) {
                        $availableStock += $existingProduct->pivot->quantity;
                    }
                }
                
                // Vérifier si le stock est suffisant
                if ($availableStock < $item['quantity']) {
                    DB::rollBack();
                    $this->addError('selectedProducts', "Stock insuffisant pour {$product->name}. Disponible: {$availableStock}");
                    return;
                }
                
                $itemPrice = $product->price * $item['quantity'];
                $totalAmount += $itemPrice;
                
                $productsData[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ];
            }
            
            if ($this->isEditing) {
                $sale = Sale::findOrFail($this->saleId);
                
                // Restaurer les stocks des produits de la vente actuelle
                foreach ($sale->products as $product) {
                    $product->increment('stock_quantity', $product->pivot->quantity);
                }
                
                // Mettre à jour la vente
                $sale->update([
                    'client_id' => $this->client_id,
                    'pharmacist_id' => $this->pharmacist_id,
                    'sale_date' => $this->sale_date,
                    'total_amount' => $totalAmount,
                    'payment_method' => $this->payment_method,
                    'status' => $this->status,
                    'notes' => $this->notes,
                ]);
                
                // Synchroniser les produits
                $sale->products()->sync($productsData);
                
                // Mettre à jour les stocks
                foreach ($products as $item) {
                    $product = Product::findOrFail($item['id']);
                    $product->decrement('stock_quantity', $item['quantity']);
                }
                
                session()->flash('success', 'Vente mise à jour avec succès.');
            } else {
                // Créer la vente
                $sale = Sale::create([
                    'client_id' => $this->client_id,
                    'pharmacist_id' => $this->pharmacist_id,
                    'sale_date' => $this->sale_date,
                    'total_amount' => $totalAmount,
                    'payment_method' => $this->payment_method,
                    'status' => $this->status,
                    'notes' => $this->notes,
                ]);
                
                // Attacher les produits à la vente
                $sale->products()->attach($productsData);
                
                // Mettre à jour les stocks
                foreach ($products as $item) {
                    $product = Product::findOrFail($item['id']);
                    $product->decrement('stock_quantity', $item['quantity']);
                }
                
                session()->flash('success', 'Vente créée avec succès.');
                
                // Réinitialiser le formulaire
                $this->reset(['client_id', 'pharmacist_id', 'notes']);
                $this->sale_date = Carbon::now()->format('Y-m-d');
                $this->payment_method = 'cash';
                $this->status = 'completed';
                $this->selectedProducts = [
                    ['product_id' => '', 'quantity' => 1, 'price' => 0, 'subtotal' => 0]
                ];
            }
            
            DB::commit();
            $this->dispatch('refreshSales');
            
            if ($this->isEditing) {
                return redirect()->route('sales.index');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        $clients = Client::all();
        $pharmacists = Pharmacist::all();
        $products = Product::where('stock_quantity', '>', 0)->get();
        
        return view('livewire.sales.sale-form', [
            'clients' => $clients,
            'pharmacists' => $pharmacists,
            'products' => $products,
            'total' => $this->getTotalProperty(),
        ]);
    }
}

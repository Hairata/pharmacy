<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductForm extends Component
{
    use WithFileUploads;
    
    public $productId;
    public $name;
    public $description;
    public $price;
    public $stock_quantity;
    public $expiry_date;
    public $category;
    public $manufacturer;
    public $image;
    public $image_path;
    public $isEditing = false;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'expiry_date' => 'nullable|date',
        'category' => 'nullable|string|max:255',
        'manufacturer' => 'nullable|string|max:255',
    ];
    
    public function mount($productId = null)
    {
        if ($productId) {
            $this->productId = $productId;
            $this->isEditing = true;
            $this->loadProduct();
        }
    }
    
    public function loadProduct()
    {
        $product = Product::findOrFail($this->productId);
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock_quantity = $product->stock_quantity;
        $this->expiry_date = $product->expiry_date ? $product->expiry_date->format('Y-m-d') : null;
        $this->category = $product->category;
        $this->manufacturer = $product->manufacturer;
    }
    
    public function save()
    {
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'expiry_date' => $this->expiry_date,
            'category' => $this->category,
            'manufacturer' => $this->manufacturer,
        ];
        
        if ($this->isEditing) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);
            session()->flash('success', 'Produit mis à jour avec succès.');
        } else {
            Product::create($data);
            session()->flash('success', 'Produit créé avec succès.');
            $this->reset(['name', 'description', 'price', 'stock_quantity', 'expiry_date', 'category', 'manufacturer']);
        }
        
        $this->dispatch('refreshProducts');
    }
    
    public function render()
    {
        return view('livewire.products.product-form');
    }
}

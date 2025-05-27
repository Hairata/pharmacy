<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class ProductList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $category = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'category' => ['except' => ''],
    ];
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingCategory()
    {
        $this->resetPage();
    }
    
    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        
        // Supprimer l'image associée si elle existe
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();
        
        session()->flash('success', 'Produit supprimé avec succès.');
    }
    
    public function render()
    {
        $categories = Product::select('category')->distinct()->whereNotNull('category')->pluck('category');
        
        $products = Product::query()
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('manufacturer', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($query) {
                return $query->where('category', $this->category);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        return view('livewire.products.product-list', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}

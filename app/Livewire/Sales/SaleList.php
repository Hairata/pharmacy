<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SaleList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $sortField = 'sale_date';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $status = '';
    public $dateRange = '';
    public $clientId = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'sale_date'],
        'sortDirection' => ['except' => 'desc'],
        'status' => ['except' => ''],
        'dateRange' => ['except' => ''],
        'clientId' => ['except' => ''],
    ];
    
    protected $listeners = ['refreshSales' => '$refresh'];
    
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
    
    public function updatingStatus()
    {
        $this->resetPage();
    }
    
    public function updatingDateRange()
    {
        $this->resetPage();
    }
    
    public function updatingClientId()
    {
        $this->resetPage();
    }
    
    public function deleteSale($id)
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
            
            // Supprimer la vente
            $sale->delete();
            
            DB::commit();
            
            session()->flash('success', 'Vente supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur est survenue lors de la suppression de la vente.');
        }
    }
    
    public function render()
    {
        $clients = Client::all();
        
        $query = Sale::with(['client', 'pharmacist'])
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->whereHas('client', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('pharmacist', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('id', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->clientId, function ($query) {
                return $query->where('client_id', $this->clientId);
            })
            ->when($this->dateRange, function ($query) {
                $dates = explode(' to ', $this->dateRange);
                if (count($dates) === 2) {
                    return $query->whereBetween('sale_date', [$dates[0], $dates[1]]);
                }
                return $query;
            });
        
        $sales = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        return view('livewire.sales.sale-list', [
            'sales' => $sales,
            'clients' => $clients,
        ]);
    }
}

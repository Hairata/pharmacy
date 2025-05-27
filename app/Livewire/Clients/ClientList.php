<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $city = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'city' => ['except' => ''],
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
    
    public function updatingCity()
    {
        $this->resetPage();
    }
    
    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        
        // Vérifier si le client a des ventes associées
        if ($client->sales()->exists()) {
            session()->flash('error', 'Impossible de supprimer ce client car il a des ventes associées.');
            return;
        }
        
        $client->delete();
        
        session()->flash('success', 'Client supprimé avec succès.');
    }
    
    public function render()
    {
        $cities = Client::select('city')->distinct()->whereNotNull('city')->pluck('city');
        
        $clients = Client::query()
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('health_insurance_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->city, function ($query) {
                return $query->where('city', $this->city);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        return view('livewire.clients.client-list', [
            'clients' => $clients,
            'cities' => $cities,
        ]);
    }
}

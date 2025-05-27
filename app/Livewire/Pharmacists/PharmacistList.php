<?php

namespace App\Livewire\Pharmacists;

use App\Models\Pharmacist;
use Livewire\Component;
use Livewire\WithPagination;

class PharmacistList extends Component
{
    use WithPagination;
    
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $specialization = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'specialization' => ['except' => ''],
    ];
    
    protected $listeners = ['refreshPharmacists' => '$refresh'];
    
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
    
    public function updatingSpecialization()
    {
        $this->resetPage();
    }
    
    public function deletePharmacist($id)
    {
        $pharmacist = Pharmacist::findOrFail($id);
        
        // Vérifier si le pharmacien a des ventes associées
        if ($pharmacist->sales()->exists()) {
            session()->flash('error', 'Impossible de supprimer ce pharmacien car il a des ventes associées.');
            return;
        }
        
        $pharmacist->delete();
        
        session()->flash('success', 'Pharmacien supprimé avec succès.');
    }
    
    public function render()
    {
        $specializations = Pharmacist::select('specialization')
            ->distinct()
            ->whereNotNull('specialization')
            ->pluck('specialization');
        
        $pharmacists = Pharmacist::query()
            ->when($this->search, function ($query) {
                return $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('license_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->specialization, function ($query) {
                return $query->where('specialization', $this->specialization);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        return view('livewire.pharmacists.pharmacist-list', [
            'pharmacists' => $pharmacists,
            'specializations' => $specializations,
        ]);
    }
}

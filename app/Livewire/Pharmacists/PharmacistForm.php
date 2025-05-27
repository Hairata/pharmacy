<?php

namespace App\Livewire\Pharmacists;

use App\Models\Pharmacist;
use Livewire\Component;

class PharmacistForm extends Component
{
    public $pharmacistId;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $license_number;
    public $specialization;
    public $hire_date;
    public $isEditing = false;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'license_number' => 'required|string|max:50',
        'specialization' => 'nullable|string|max:100',
        'hire_date' => 'nullable|date',
    ];
    
    public function mount($pharmacistId = null)
    {
        if ($pharmacistId) {
            $this->pharmacistId = $pharmacistId;
            $this->isEditing = true;
            $this->loadPharmacist();
        }
    }
    
    public function loadPharmacist()
    {
        $pharmacist = Pharmacist::findOrFail($this->pharmacistId);
        $this->name = $pharmacist->name;
        $this->email = $pharmacist->email;
        $this->phone = $pharmacist->phone;
        $this->address = $pharmacist->address;
        $this->license_number = $pharmacist->license_number;
        $this->specialization = $pharmacist->specialization;
        $this->hire_date = $pharmacist->hire_date ? $pharmacist->hire_date->format('Y-m-d') : null;
    }
    
    public function save()
    {
        if ($this->isEditing) {
            $this->rules['email'] = 'nullable|email|max:255|unique:pharmacists,email,' . $this->pharmacistId;
            $this->rules['license_number'] = 'required|string|max:50|unique:pharmacists,license_number,' . $this->pharmacistId;
        } else {
            $this->rules['email'] = 'nullable|email|max:255|unique:pharmacists';
            $this->rules['license_number'] = 'required|string|max:50|unique:pharmacists';
        }
        
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'license_number' => $this->license_number,
            'specialization' => $this->specialization,
            'hire_date' => $this->hire_date,
        ];
        
        if ($this->isEditing) {
            $pharmacist = Pharmacist::findOrFail($this->pharmacistId);
            $pharmacist->update($data);
            session()->flash('success', 'Pharmacien mis à jour avec succès.');
        } else {
            Pharmacist::create($data);
            session()->flash('success', 'Pharmacien créé avec succès.');
            $this->reset(['name', 'email', 'phone', 'address', 'license_number', 'specialization', 'hire_date']);
        }
        
        $this->dispatch('refreshPharmacists');
    }
    
    public function render()
    {
        return view('livewire.pharmacists.pharmacist-form');
    }
}

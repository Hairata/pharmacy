<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class ClientForm extends Component
{
    public $clientId;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $postal_code;
    public $date_of_birth;
    public $health_insurance_number;
    public $isEditing = false;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'date_of_birth' => 'nullable|date',
        'health_insurance_number' => 'nullable|string|max:50',
    ];
    
    public function mount($clientId = null)
    {
        if ($clientId) {
            $this->clientId = $clientId;
            $this->isEditing = true;
            $this->loadClient();
        }
    }
    
    public function loadClient()
    {
        $client = Client::findOrFail($this->clientId);
        $this->name = $client->name;
        $this->email = $client->email;
        $this->phone = $client->phone;
        $this->address = $client->address;
        $this->city = $client->city;
        $this->postal_code = $client->postal_code;
        $this->date_of_birth = $client->date_of_birth ? $client->date_of_birth->format('Y-m-d') : null;
        $this->health_insurance_number = $client->health_insurance_number;
    }
    
    public function save()
    {
        if ($this->isEditing) {
            $this->rules['email'] = 'nullable|email|max:255|unique:clients,email,' . $this->clientId;
        } else {
            $this->rules['email'] = 'nullable|email|max:255|unique:clients';
        }
        
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'date_of_birth' => $this->date_of_birth,
            'health_insurance_number' => $this->health_insurance_number,
        ];
        
        if ($this->isEditing) {
            $client = Client::findOrFail($this->clientId);
            $client->update($data);
            session()->flash('success', 'Client mis à jour avec succès.');
        } else {
            Client::create($data);
            session()->flash('success', 'Client créé avec succès.');
            $this->reset(['name', 'email', 'phone', 'address', 'city', 'postal_code', 'date_of_birth', 'health_insurance_number']);
        }
        
        $this->dispatch('refreshClients');
    }
    
    public function render()
    {
        return view('livewire.clients.client-form');
    }
}

<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Pharmacist;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Obtient les statistiques pour le tableau de bord.
     *
     * @return array
     */
    public function getStats()
    {
        return [
            'total_products' => Product::count(),
            'total_clients' => Client::count(),
            'total_pharmacists' => Pharmacist::count(),
            'total_sales' => Sale::count(),
            'total_revenue' => Sale::sum('total_amount'),
            'recent_sales' => Sale::with(['client', 'pharmacist'])->latest()->take(5)->get(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->get(),
        ];
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.dashboard', [
            'stats' => $this->getStats(),
        ]);
    }
}

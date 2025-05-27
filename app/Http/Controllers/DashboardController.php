<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Pharmacist;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord avec les statistiques.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupère les statistiques pour le tableau de bord
        $stats = [
            'total_products' => Product::count(),
            'total_clients' => Client::count(),
            'total_pharmacists' => Pharmacist::count(),
            'total_sales' => Sale::count(),
            'total_revenue' => Sale::sum('total_amount'),
            'recent_sales' => Sale::with(['client', 'pharmacist'])->latest()->take(5)->get(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->get(),
        ];
        
        return view('dashboard', compact('stats'));
    }
}

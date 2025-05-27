<?php

namespace App\Livewire\Dashboard;

use App\Models\Sale;
use App\Models\Pharmacist;
use Carbon\Carbon;
use Livewire\Component;

class SalesStats extends Component
{
    public $period = 'month';
    public $pharmacistId = null;
    public $startDate;
    public $endDate;
    
    public function mount()
    {
        $this->setDefaultDates();
    }
    
    public function setDefaultDates()
    {
        switch ($this->period) {
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Ne rien faire, laisser les dates telles quelles
                break;
        }
    }
    
    public function updatedPeriod()
    {
        if ($this->period !== 'custom') {
            $this->setDefaultDates();
        }
    }
    
    public function render()
    {
        $pharmacists = Pharmacist::orderBy('name')->get();
        
        $query = Sale::query()
            ->whereBetween('sale_date', [$this->startDate, $this->endDate])
            ->where('status', 'completed');
            
        if ($this->pharmacistId) {
            $query->where('pharmacist_id', $this->pharmacistId);
        }
        
        $salesData = $query->get();
        
        // Statistiques générales
        $totalSales = $salesData->count();
        $totalRevenue = $salesData->sum('total_amount');
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        // Ventes par jour pour le graphique
        $salesByDate = $salesData->groupBy(function ($sale) {
            return Carbon::parse($sale->sale_date)->format('Y-m-d');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount')
            ];
        });
        
        // Ventes par pharmacien
        $salesByPharmacist = $salesData->groupBy('pharmacist_id')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
                'pharmacist_name' => $group->first()->pharmacist->name
            ];
        });
        
        return view('livewire.dashboard.sales-stats', [
            'pharmacists' => $pharmacists,
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'averageTicket' => $averageTicket,
            'salesByDate' => $salesByDate,
            'salesByPharmacist' => $salesByPharmacist
        ]);
    }
}

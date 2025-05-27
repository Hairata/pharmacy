@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Détails du pharmacien</h1>
            <div class="flex space-x-2">
                <a href="{{ route('pharmacists.edit', $pharmacist->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('pharmacists.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                        <div class="space-y-4">
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Nom</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->name }}</span>
                            </div>
                            
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Email</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->email ?? 'Non spécifié' }}</span>
                            </div>
                            
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Téléphone</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->phone }}</span>
                            </div>
                            
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Adresse</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->address ?? 'Non spécifiée' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations professionnelles</h3>
                        <div class="space-y-4">
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Numéro de licence</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->license_number }}</span>
                            </div>
                            
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Spécialisation</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->specialization ?? 'Non spécifiée' }}</span>
                            </div>
                            
                            <div>
                                <span class="block text-sm font-medium text-gray-500">Date d'embauche</span>
                                <span class="block mt-1 text-sm text-gray-900">{{ $pharmacist->hire_date ? $pharmacist->hire_date->format('d/m/Y') : 'Non spécifiée' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des ventes</h3>
                
                @if($pharmacist->sales->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pharmacist->sales as $sale)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->sale_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->client->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($sale->total_amount, 2) }} Fcfa</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : ($sale->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $sale->status === 'completed' ? 'Complétée' : ($sale->status === 'pending' ? 'En attente' : 'Annulée') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('sales.show', $sale->id) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500">
                        Ce pharmacien n'a pas encore effectué de vente.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Détails de la vente #{{ $sale->id }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('sales.edit', $sale->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Informations générales</h3>
                        <div class="mt-2 space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">Date de vente:</span>
                                <p class="font-medium">{{ $sale->sale_date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Montant total:</span>
                                <p class="font-medium">{{ number_format($sale->total_amount, 2) }} Fcfa</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Méthode de paiement:</span>
                                <p class="font-medium">
                                    {{ $sale->payment_method === 'cash' ? 'Espèces' : ($sale->payment_method === 'card' ? 'Carte bancaire' : 'Assurance') }}
                                </p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Statut:</span>
                                <p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sale->status === 'completed' ? 'bg-green-100 text-green-800' : ($sale->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $sale->status === 'completed' ? 'Complétée' : ($sale->status === 'pending' ? 'En attente' : 'Annulée') }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Client</h3>
                        <div class="mt-2 space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">Nom:</span>
                                <p class="font-medium">{{ $sale->client->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Email:</span>
                                <p class="font-medium">{{ $sale->client->email }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Téléphone:</span>
                                <p class="font-medium">{{ $sale->client->phone }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Adresse:</span>
                                <p class="font-medium">{{ $sale->client->address }}, {{ $sale->client->city }} {{ $sale->client->postal_code }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Pharmacien</h3>
                        <div class="mt-2 space-y-2">
                            <div>
                                <span class="text-sm text-gray-500">Nom:</span>
                                <p class="font-medium">{{ $sale->pharmacist->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Email:</span>
                                <p class="font-medium">{{ $sale->pharmacist->email }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Téléphone:</span>
                                <p class="font-medium">{{ $sale->pharmacist->phone }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Spécialisation:</span>
                                <p class="font-medium">{{ $sale->pharmacist->specialization ?? 'Non spécifiée' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if ($sale->notes)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                    <div class="mt-2 p-4 bg-gray-50 rounded-md">
                        {{ $sale->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Produits</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($sale->products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($product->image_path)
                                                <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $product->category }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($product->pivot->price, 2) }} Fcfa
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product->pivot->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ number_format($product->pivot->price * $product->pivot->quantity, 2) }} Fcfa
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Total</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    {{ number_format($sale->total_amount, 2) }} Fcfa
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

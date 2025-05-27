@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Détails du produit</h1>
            <div class="flex space-x-2">
                <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col md:flex-row">
                    @if ($product->image_path)
                        <div class="md:w-1/3 mb-4 md:mb-0 md:mr-6">
                            <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg object-cover shadow-md">
                        </div>
                    @endif
                    <div class="md:w-2/3">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $product->name }}</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Prix</h3>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($product->price, 2) }} Fcfa</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Stock</h3>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->stock_quantity > 10 ? 'bg-green-100 text-green-800' : ($product->stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $product->stock_quantity }} unités
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Catégorie</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->category ?? 'Non catégorisé' }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Fabricant</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->manufacturer ?? 'Non spécifié' }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Date d'expiration</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->expiry_date ? $product->expiry_date->format('d/m/Y') : 'Non spécifiée' }}</p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Dernière mise à jour</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <div class="mt-1 prose prose-sm text-gray-900">
                                {{ $product->description ?? 'Aucune description disponible.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

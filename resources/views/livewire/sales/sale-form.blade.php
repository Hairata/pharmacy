<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">{{ $isEditing ? 'Modifier la vente' : 'Nouvelle vente' }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $isEditing ? 'Modifier les informations de la vente existante.' : 'Créer une nouvelle vente.' }}</p>
        </div>

        <!-- Messages de notification -->
        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @error('error')
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ $message }}</p>
            </div>
        @enderror

        @error('selectedProducts')
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ $message }}</p>
            </div>
        @enderror

        <!-- Formulaire -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form wire:submit="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Client -->
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Client</label>
                            <select wire:model="client_id" id="client_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->phone }}</option>
                                @endforeach
                            </select>
                            @error('client_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Pharmacien -->
                        <div>
                            <label for="pharmacist_id" class="block text-sm font-medium text-gray-700">Pharmacien</label>
                            <select wire:model="pharmacist_id" id="pharmacist_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Sélectionner un pharmacien</option>
                                @foreach($pharmacists as $pharmacist)
                                    <option value="{{ $pharmacist->id }}">{{ $pharmacist->name }}</option>
                                @endforeach
                            </select>
                            @error('pharmacist_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Date de vente -->
                        <div>
                            <label for="sale_date" class="block text-sm font-medium text-gray-700">Date de vente</label>
                            <input wire:model="sale_date" type="date" id="sale_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('sale_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Méthode de paiement -->
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">Méthode de paiement</label>
                            <select wire:model="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="cash">Espèces</option>
                                <option value="card">Carte bancaire</option>
                                <option value="insurance">Assurance</option>
                            </select>
                            @error('payment_method') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                            <select wire:model="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="completed">Complétée</option>
                                <option value="pending">En attente</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea wire:model="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Produits -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-medium text-gray-900">Produits</h3>
                            <button type="button" wire:click="addProduct" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Ajouter un produit
                            </button>
                        </div>

                        <div class="bg-gray-50 rounded-md p-4">
                            <div class="space-y-4">
                                @foreach($selectedProducts as $index => $product)
                                    <div class="flex flex-col md:flex-row md:items-center gap-4 p-3 bg-white rounded-md shadow-sm">
                                        <div class="flex-grow md:w-1/3">
                                            <label for="product_{{ $index }}" class="block text-sm font-medium text-gray-700">Produit</label>
                                            <select wire:model.live="selectedProducts.{{ $index }}.product_id" id="product_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">Sélectionner un produit</option>
                                                @foreach($products as $productOption)
                                                    <option value="{{ $productOption->id }}" {{ isset($product['stock']) && $product['stock'] < 1 && $productOption->id !== $product['product_id'] ? 'disabled' : '' }}>
                                                        {{ $productOption->name }} - Stock: {{ $productOption->stock_quantity }} - {{ number_format($productOption->price, 2) }} Fcfa
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('selectedProducts.' . $index . '.product_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="md:w-1/6">
                                            <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">Quantité</label>
                                            <input wire:model.live="selectedProducts.{{ $index }}.quantity" type="number" min="1" id="quantity_{{ $index }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            @error('selectedProducts.' . $index . '.quantity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="md:w-1/6">
                                            <label class="block text-sm font-medium text-gray-700">Prix unitaire</label>
                                            <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md text-sm text-gray-700">
                                                {{ isset($product['price']) ? number_format($product['price'], 2) : '0.00' }} Fcfa
                                            </div>
                                        </div>
                                        <div class="md:w-1/6">
                                            <label class="block text-sm font-medium text-gray-700">Sous-total</label>
                                            <div class="mt-1 block w-full py-2 px-3 bg-gray-100 rounded-md text-sm text-gray-700">
                                                {{ isset($product['subtotal']) ? number_format($product['subtotal'], 2) : '0.00' }} Fcfa
                                            </div>
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" wire:click="removeProduct({{ $index }})" class="text-red-600 hover:text-red-900 {{ count($selectedProducts) <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($selectedProducts) <= 1 ? 'disabled' : '' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 flex justify-end">
                                <div class="text-right">
                                    <span class="block text-sm font-medium text-gray-700">Total:</span>
                                    <span class="block text-xl font-bold text-gray-900">{{ number_format($total, 2) }} Fcfa</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('sales.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

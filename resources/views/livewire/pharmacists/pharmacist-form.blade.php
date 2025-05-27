<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">{{ $isEditing ? 'Modifier le pharmacien' : 'Nouveau pharmacien' }}</h1>
            <a href="{{ route('pharmacists.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
        </div>

        <!-- Messages de notification -->
        @if (session()->has('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations personnelles -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nom <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="text" wire:model="name" id="name" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Nom complet">
                                </div>
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <div class="mt-1">
                                    <input type="email" wire:model="email" id="email" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="email@exemple.com">
                                </div>
                                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="text" wire:model="phone" id="phone" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="+33 1 23 45 67 89">
                                </div>
                                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Adresse</label>
                                <div class="mt-1">
                                    <input type="text" wire:model="address" id="address" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="123 rue Exemple">
                                </div>
                                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <!-- Informations professionnelles -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-medium text-gray-900">Informations professionnelles</h3>
                            
                            <div>
                                <label for="license_number" class="block text-sm font-medium text-gray-700">Numéro de licence <span class="text-red-500">*</span></label>
                                <div class="mt-1">
                                    <input type="text" wire:model="license_number" id="license_number" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Numéro de licence">
                                </div>
                                @error('license_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="specialization" class="block text-sm font-medium text-gray-700">Spécialisation</label>
                                <div class="mt-1">
                                    <input type="text" wire:model="specialization" id="specialization" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Spécialisation">
                                </div>
                                @error('specialization') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-gray-700">Date d'embauche</label>
                                <div class="mt-1">
                                    <input type="date" wire:model="hire_date" id="hire_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                                @error('hire_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('pharmacists.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

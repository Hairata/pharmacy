<div class="bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6">
        <h2 class="text-lg leading-6 font-medium text-gray-900">Statistiques des ventes</h2>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">Analyse des ventes par période et par pharmacien</p>
    </div>
    
    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Sélection de la période -->
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700">Période</label>
                <select wire:model.live="period" id="period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                    <option value="custom">Personnalisée</option>
                </select>
            </div>
            
            <!-- Sélection du pharmacien -->
            <div>
                <label for="pharmacistId" class="block text-sm font-medium text-gray-700">Pharmacien</label>
                <select wire:model.live="pharmacistId" id="pharmacistId" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Tous les pharmaciens</option>
                    @foreach($pharmacists as $pharmacist)
                        <option value="{{ $pharmacist->id }}">{{ $pharmacist->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Dates personnalisées -->
            @if($period === 'custom')
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-700">Date de début</label>
                        <input type="date" wire:model.live="startDate" id="startDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="endDate" class="block text-sm font-medium text-gray-700">Date de fin</label>
                        <input type="date" wire:model.live="endDate" id="endDate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Statistiques générales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Nombre de ventes</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalSales }}</dd>
                    </dl>
                </div>
            </div>
            
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Chiffre d'affaires</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($totalRevenue, 2) }} Fcfa</dd>
                    </dl>
                </div>
            </div>
            
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Panier moyen</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($averageTicket, 2) }} Fcfa</dd>
                    </dl>
                </div>
            </div>
        </div>
        
        <!-- Ventes par pharmacien -->
        @if($salesByPharmacist->count() > 0 && !$pharmacistId)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ventes par pharmacien</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pharmacien</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de ventes</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'affaires</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Panier moyen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesByPharmacist as $pharmacistId => $data)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['pharmacist_name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['count'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($data['total'], 2) }} Fcfa</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($data['count'] > 0 ? $data['total'] / $data['count'] : 0, 2) }} Fcfa</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Ventes par jour -->
        @if($salesByDate->count() > 0)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ventes par jour</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de ventes</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salesByDate as $date => $data)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['count'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($data['total'], 2) }} Fcfa</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-4 text-gray-500">
                Aucune vente trouvée pour cette période.
            </div>
        @endif
    </div>
</div>

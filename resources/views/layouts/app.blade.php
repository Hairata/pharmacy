<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Gestion de Pharmacie') }} - @yield('title')</title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600">
                                Gestion de Pharmacie
                            </a>
                        </div>
                        <nav class="ml-6 flex space-x-4 items-center">
                            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                                Tableau de bord
                            </a>
                            <a href="{{ route('products.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('products.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                                Produits
                            </a>
                            <a href="{{ route('clients.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('clients.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                                Clients
                            </a>
                            <a href="{{ route('pharmacists.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('pharmacists.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                                Pharmaciens
                            </a>
                            <a href="{{ route('sales.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('sales.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                                Ventes
                            </a>
                        </nav>
                    </div>
                    <div class="flex items-center">
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" class="flex items-center max-w-xs bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Ouvrir le menu utilisateur</span>
                                    <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center">
                                        <span>{{ auth()->user() ? substr(auth()->user()->name, 0, 1) : 'U' }}</span>
                                    </div>
                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ auth()->user() ? auth()->user()->name : 'Utilisateur' }}</span>
                                </button>
                            </div>
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-cloak
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                 role="menu" 
                                 aria-orientation="vertical" 
                                 aria-labelledby="user-menu-button" 
                                 tabindex="-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="px-4 py-6 sm:px-0">
                    <div class="border-4 border-dashed border-gray-200 rounded-lg">
                        <div class="p-6">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} Gestion de Pharmacie. Tous droits réservés.
                </p>
            </div>
        </footer>
    </div>

    <!-- Alpine.js via CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    @stack('scripts')
</body>
</html>

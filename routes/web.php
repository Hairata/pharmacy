<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route d'accueil - redirige vers le tableau de bord
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes d'authentification (à activer quand l'authentification sera mise en place)
// Route::middleware('auth')->group(function () {

// Tableau de bord
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Routes pour les produits
Route::resource('products', ProductController::class);

// Routes pour les clients
Route::resource('clients', ClientController::class);

// Routes pour les pharmaciens
Route::resource('pharmacists', PharmacistController::class);

// Routes pour les ventes
Route::resource('sales', SaleController::class);
Route::get('/sales-stats', [SaleController::class, 'stats'])->name('sales.stats');

// Routes pour le profil utilisateur
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

// });

// Route temporaire pour tester
Route::get('/test', function () {
    return view('hello', ['users' => ['Test User']]);
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
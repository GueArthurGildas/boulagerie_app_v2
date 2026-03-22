<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RecetteController;
use App\Http\Controllers\MatierePremiereController;
use App\Http\Controllers\ProduitController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Production
    Route::resource('productions', ProductionController::class)->except(['edit', 'update', 'destroy']);
    Route::post('productions/{production}/close', [ProductionController::class, 'close'])->name('productions.close');
    Route::post('productions/{production}/incidents', [ProductionController::class, 'storeIncident'])->name('productions.incidents.store');
    Route::get('api/recettes/{recette}/stock', [ProductionController::class, 'verifierStock'])->name('api.recettes.stock');

    // Recettes
    Route::resource('recettes', RecetteController::class);

    // Matières premières
    Route::resource('matieres-premieres', MatierePremiereController::class);

    // Produits
    Route::resource('produits', ProduitController::class);
});

require __DIR__.'/auth.php';

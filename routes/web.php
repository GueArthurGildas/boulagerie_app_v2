<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\RecetteController;
use App\Http\Controllers\MatierePremiereController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\FournisseurController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Production ───────────────────────────────────────────────
    Route::resource('productions', ProductionController::class)->except(['edit', 'update', 'destroy']);
    Route::post('productions/{production}/close',           [ProductionController::class, 'close'])->name('productions.close');
    Route::post('productions/{production}/correct',         [ProductionController::class, 'correct'])->name('productions.correct');
    Route::post('productions/{production}/update-invendus', [ProductionController::class, 'updateInvendus'])->name('productions.update-invendus');
    Route::post('productions/{production}/annuler',         [ProductionController::class, 'annuler'])->name('productions.annuler');
    Route::post('productions/{production}/incidents',       [ProductionController::class, 'storeIncident'])->name('productions.incidents.store');
    Route::get('api/recettes/{recette}/stock',              [ProductionController::class, 'verifierStock'])->name('api.recettes.stock');
    Route::resource('recettes', RecetteController::class);
    Route::resource('matieres-premieres', MatierePremiereController::class);
    Route::resource('produits', ProduitController::class);

    // ── Dépenses ─────────────────────────────────────────────────
    Route::get('depenses/rapport',     [DepenseController::class, 'rapport'])->name('depenses.rapport');
    Route::get('depenses/categories',  [DepenseController::class, 'categories'])->name('depenses.categories');
    Route::post('depenses/categories', [DepenseController::class, 'storeCategorie'])->name('depenses.categories.store');
    Route::delete('depenses/categories/{categorieDepense}', [DepenseController::class, 'destroyCategorie'])->name('depenses.categories.destroy');
    Route::resource('depenses', DepenseController::class);
    Route::post('depenses/{depense}/valider', [DepenseController::class, 'valider'])->name('depenses.valider');
    Route::post('depenses/{depense}/rejeter', [DepenseController::class, 'rejeter'])->name('depenses.rejeter');
    Route::post('depenses/{depense}/cloner',  [DepenseController::class, 'cloner'])->name('depenses.cloner');

    // ── Fournisseurs ──────────────────────────────────────────────
    Route::resource('fournisseurs', FournisseurController::class);

    // Achats
    Route::get ('fournisseurs/{fournisseur}/achats',                    [FournisseurController::class, 'achats'])->name('fournisseurs.achats');
    Route::post('fournisseurs/{fournisseur}/achats',                    [FournisseurController::class, 'storeAchat'])->name('fournisseurs.achat.store');
    Route::get ('fournisseurs/{fournisseur}/achats/{achat}',            [FournisseurController::class, 'showAchat'])->name('fournisseurs.achat.show');
    Route::post('fournisseurs/{fournisseur}/achats/{achat}/valider',    [FournisseurController::class, 'validerAchat'])->name('fournisseurs.achat.valider');
    Route::delete('fournisseurs/{fournisseur}/achats/{achat}/annuler',  [FournisseurController::class, 'annulerAchat'])->name('fournisseurs.achat.annuler');

    // Règlements
    Route::post('fournisseurs/{fournisseur}/reglements', [FournisseurController::class, 'storeReglement'])->name('fournisseurs.reglement.store');

    // Lier dépense
    Route::post('fournisseurs/{fournisseur}/lier-depense', [FournisseurController::class, 'lierDepense'])->name('fournisseurs.lier-depense');
});

require __DIR__.'/auth.php';

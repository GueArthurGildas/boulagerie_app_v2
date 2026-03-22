<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\MatierePremiere;
use App\Models\Recette;
use App\Models\Produit;
use App\Models\ProductionLigne;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();

        $stats = [
            'fournees_jour'      => Production::whereDate('date_production', $today)->count(),
            'fournees_terminees' => Production::whereDate('date_production', $today)->where('statut','terminee')->count(),
            'pieces_produites'   => Production::whereDate('date_production', $today)->where('statut','terminee')->sum('nb_pieces_produites'),
            'rendement_moyen'    => round(Production::whereDate('date_production', $today)->where('statut','terminee')->avg('rendement') ?? 0, 1),
            'alertes_stock'      => MatierePremiere::whereColumn('stock_actuel', '<=', 'stock_minimum')->count(),
            'recettes_actives'   => Recette::where('actif', true)->count(),
            'produits_actifs'    => Produit::where('actif', true)->count(),
        ];

        $productions_jour = Production::with(['recette'])
            ->whereDate('date_production', $today)
            ->orderByDesc('created_at')
            ->get();

        $alertes_stock = MatierePremiere::whereColumn('stock_actuel', '<=', 'stock_minimum')
            ->orderBy('stock_actuel')
            ->get();

        $top_produits = ProductionLigne::select('produit_id', DB::raw('SUM(quantite_produite) as total'))
            ->whereHas('production', fn($q) => $q->whereBetween('date_production', [$weekStart, $today]))
            ->with('produit')
            ->groupBy('produit_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'productions_jour', 'alertes_stock', 'top_produits'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Recette;
use App\Models\Produit;
use App\Services\ProductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function __construct(protected ProductionService $productionService) {}

    public function index(Request $request)
    {
        $query = Production::with(['recette', 'createdBy'])->orderByDesc('date_production')->orderByDesc('id');

        if ($request->filled('date'))   $query->whereDate('date_production', $request->date);
        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('equipe')) $query->where('equipe', $request->equipe);

        $productions = $query->paginate(20);
        return view('production.index', compact('productions'));
    }

    public function create()
    {
        $recettes = Recette::with('lignes.matierePremiere')->where('actif', true)->get();
        return view('production.create', compact('recettes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recette_id' => 'required|exists:recettes,id',
            'equipe'     => 'required|in:jour,nuit',
            'notes'      => 'nullable|string',
        ]);

        try {
            $production = $this->productionService->startFournee(
                $request->recette_id,
                $request->equipe,
                $request->notes
            );
            return redirect()->route('productions.show', $production)
                ->with('success', 'Fournée démarrée ! Les matières ont été consommées.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Production $production)
    {
        $production->load(['recette.lignes.matierePremiere', 'lignes.produit', 'incidents', 'createdBy']);
        $produits = Produit::where('actif', true)->get();
        return view('production.show', compact('production', 'produits'));
    }

    /**
     * Clôturer une fournée en cours
     */
    public function close(Request $request, Production $production)
    {
        $request->validate([
            'lignes'                     => 'required|array|min:1',
            'lignes.*.produit_id'        => 'required|exists:produits,id',
            'lignes.*.quantite_produite' => 'required|integer|min:0',
            'lignes.*.quantite_invendue' => 'nullable|integer|min:0',
        ]);

        try {
            $this->productionService->closeFournee($production, $request->lignes);
            return redirect()->route('productions.show', $production)
                ->with('success', 'Fournée clôturée avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Corriger les quantités produites d'une fournée terminée
     */
    public function correct(Request $request, Production $production)
    {
        $request->validate([
            'lignes'                     => 'required|array|min:1',
            'lignes.*.produit_id'        => 'required|exists:produits,id',
            'lignes.*.quantite_produite' => 'required|integer|min:0',
            'lignes.*.quantite_invendue' => 'nullable|integer|min:0',
            'motif_correction'           => 'required|string|min:5|max:255',
        ]);

        try {
            $this->productionService->correctFournee($production, $request->lignes, $request->motif_correction);
            return redirect()->route('productions.show', $production)
                ->with('success', 'Fournée corrigée. Note : le stock n\'est pas modifié par cette correction.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Mettre à jour uniquement les invendus
     */
    public function updateInvendus(Request $request, Production $production)
    {
        $request->validate([
            'lignes'                     => 'required|array|min:1',
            'lignes.*.ligne_id'          => 'required|exists:production_lignes,id',
            'lignes.*.quantite_invendue' => 'required|integer|min:0',
        ]);

        try {
            $this->productionService->updateInvendus($production, $request->lignes);
            return redirect()->route('productions.show', $production)
                ->with('success', 'Invendus mis à jour avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Annuler une fournée et restituer le stock
     */
    public function annuler(Request $request, Production $production)
    {
        $request->validate([
            'motif_annulation' => 'required|string|min:5|max:255',
        ]);

        try {
            $this->productionService->annulerFournee($production, $request->motif_annulation);
            return redirect()->route('productions.index')
                ->with('success', 'Fournée annulée. Le stock des matières a été restitué.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Enregistrer un incident
     */
    public function storeIncident(Request $request, Production $production)
    {
        $request->validate([
            'type_incident'       => 'required|string|max:100',
            'description'         => 'nullable|string',
            'duree_arret_minutes' => 'nullable|integer|min:0',
            'impact_fcfa'         => 'nullable|integer|min:0',
        ]);

        $production->incidents()->create([
            'type_incident'       => $request->type_incident,
            'description'         => $request->description,
            'duree_arret_minutes' => $request->duree_arret_minutes ?? 0,
            'impact_fcfa'         => $request->impact_fcfa ?? 0,
            'created_by'          => Auth::id(),
        ]);

        return back()->with('success', 'Incident enregistré.');
    }

    /**
     * Vérifier stock disponible pour une recette (API)
     */
    public function verifierStock(Recette $recette)
    {
        $recette->load('lignes.matierePremiere');
        $dispo = $this->productionService->verifierDisponibilite($recette);
        return response()->json($dispo);
    }
}

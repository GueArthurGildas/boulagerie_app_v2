<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Achat;
use App\Models\AchatLigne;
use App\Models\MatierePremiere;
use App\Models\ReglementFournisseur;
use App\Models\Depense;
use App\Models\CategorieDepense;
use App\Services\FournisseurService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FournisseurController extends Controller
{
    public function __construct(protected FournisseurService $fournisseurService) {}

    /* ── FOURNISSEURS ─────────────────────────────────────────── */

    public function index()
    {
        $fournisseurs = Fournisseur::withCount('achats')
            ->orderBy('nom')
            ->paginate(20);

        $totalDu = Fournisseur::sum('solde_du');

        return view('fournisseurs.index', compact('fournisseurs', 'totalDu'));
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'            => 'required|string|max:150',
            'telephone'      => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'adresse'        => 'nullable|string|max:255',
            'ville'          => 'nullable|string|max:100',
            'contact_nom'    => 'nullable|string|max:150',
            'type'           => 'nullable|string|max:50',
            'plafond_credit' => 'nullable|integer|min:0',
            'notes'          => 'nullable|string',
        ]);

        Fournisseur::create(array_merge($request->only([
            'nom', 'telephone', 'email', 'adresse', 'ville',
            'contact_nom', 'type', 'plafond_credit', 'notes',
        ]), ['created_by' => Auth::id()]));

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load([
            'achats' => fn($q) => $q->with('lignes')->orderByDesc('date_achat')->limit(10),
            'reglements' => fn($q) => $q->orderByDesc('date_reglement')->limit(10),
        ]);

        $depenses = Depense::where('fournisseur_id', $fournisseur->id)
            ->with('categorie')
            ->orderByDesc('date_depense')
            ->limit(10)
            ->get();

        $totalAchats     = $fournisseur->achats()->where('statut', '!=', 'brouillon')->sum('montant_total');
        $totalReglements = $fournisseur->reglements()->sum('montant');

        return view('fournisseurs.show', compact(
            'fournisseur', 'depenses', 'totalAchats', 'totalReglements'
        ));
    }

    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom'            => 'required|string|max:150',
            'telephone'      => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:150',
            'adresse'        => 'nullable|string|max:255',
            'ville'          => 'nullable|string|max:100',
            'contact_nom'    => 'nullable|string|max:150',
            'type'           => 'nullable|string|max:50',
            'plafond_credit' => 'nullable|integer|min:0',
            'notes'          => 'nullable|string',
        ]);

        $fournisseur->update(array_merge($request->only([
            'nom', 'telephone', 'email', 'adresse', 'ville',
            'contact_nom', 'type', 'plafond_credit', 'notes',
        ]), ['updated_by' => Auth::id()]));

        return redirect()->route('fournisseurs.show', $fournisseur)
            ->with('success', 'Fournisseur mis à jour.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        if ($fournisseur->achats()->exists()) {
            return back()->withErrors(['error' => 'Impossible : ce fournisseur a des achats enregistrés.']);
        }
        $fournisseur->delete();
        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur supprimé.');
    }

    /* ── ACHATS ───────────────────────────────────────────────── */

    public function achats(Fournisseur $fournisseur)
    {
        $achats   = $fournisseur->achats()->with('lignes.matierePremiere')->orderByDesc('date_achat')->paginate(15);
        $matieres = MatierePremiere::where('actif', true)->orderBy('nom')->get();

        return view('fournisseurs.achats', compact('fournisseur', 'achats', 'matieres'));
    }

    public function storeAchat(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'reference'                          => 'nullable|string|max:50',
            'date_achat'                         => 'required|date',
            'date_echeance'                      => 'nullable|date|after_or_equal:date_achat',
            'mode_paiement'                      => 'required|in:cash,orange_money,wave,mtn_momo,banque,credit,autre',
            'notes'                              => 'nullable|string',
            'lignes'                             => 'required|array|min:1',
            'lignes.*.matiere_premiere_id'       => 'required|exists:matiere_premieres,id',
            'lignes.*.quantite'                  => 'required|numeric|min:0.001',
            'lignes.*.prix_unitaire'             => 'required|integer|min:1',
        ]);

        $achat = DB::transaction(function () use ($request, $fournisseur) {
            $achat = Achat::create([
                'fournisseur_id' => $fournisseur->id,
                'reference'      => $request->reference,
                'date_achat'     => $request->date_achat,
                'date_echeance'  => $request->date_echeance,
                'mode_paiement'  => $request->mode_paiement,
                'notes'          => $request->notes,
                'statut'         => 'brouillon',
                'created_by'     => Auth::id(),
            ]);

            foreach ($request->lignes as $ligne) {
                AchatLigne::create([
                    'achat_id'            => $achat->id,
                    'matiere_premiere_id' => $ligne['matiere_premiere_id'],
                    'quantite'            => $ligne['quantite'],
                    'prix_unitaire'       => $ligne['prix_unitaire'],
                ]);
            }

            // Calculer le montant total brouillon
            $total = collect($request->lignes)->sum(fn($l) => intval($l['quantite'] * $l['prix_unitaire']));
            $achat->update(['montant_total' => $total]);

            return $achat;
        });

        return redirect()->route('fournisseurs.achat.show', [$fournisseur, $achat])
            ->with('success', 'Achat enregistré en brouillon. Validez pour mettre à jour le stock.');
    }

    public function showAchat(Fournisseur $fournisseur, Achat $achat)
    {
        $achat->load('lignes.matierePremiere', 'reglements', 'fournisseur');
        return view('fournisseurs.achat_show', compact('fournisseur', 'achat'));
    }

    public function validerAchat(Fournisseur $fournisseur, Achat $achat)
    {
        try {
            $this->fournisseurService->validerAchat($achat);
            return redirect()->route('fournisseurs.achat.show', [$fournisseur, $achat])
                ->with('success', 'Achat validé ! Stock mis à jour et dépense créée automatiquement.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Annuler un achat en brouillon — suppression simple, aucun effet sur le stock
     */
    public function annulerAchat(Fournisseur $fournisseur, Achat $achat)
    {
        if ($achat->statut !== 'brouillon') {
            return back()->withErrors(['error' => 'Seuls les achats en brouillon peuvent être annulés. Pour un achat validé, utilisez la procédure de retour fournisseur.']);
        }

        DB::transaction(function () use ($achat) {
            $achat->lignes()->delete();
            $achat->delete();
        });

        return redirect()->route('fournisseurs.show', $fournisseur)
            ->with('success', 'Achat brouillon annulé et supprimé.');
    }

    /* ── RÈGLEMENTS ───────────────────────────────────────────── */

    public function storeReglement(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'achat_id'         => 'nullable|exists:achats,id',
            'montant'          => 'required|integer|min:1',
            'date_reglement'   => 'required|date',
            'mode_paiement'    => 'required|in:cash,orange_money,wave,mtn_momo,banque,autre',
            'reference_mobile' => 'nullable|string|max:20',
            'reference_banque' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        try {
            $this->fournisseurService->creerReglement(
                array_merge($request->only([
                    'achat_id', 'montant', 'date_reglement',
                    'mode_paiement', 'reference_mobile', 'reference_banque', 'notes',
                ]), ['fournisseur_id' => $fournisseur->id])
            );
            return back()->with('success', 'Règlement enregistré et dépense créée automatiquement.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /* ── LIER DÉPENSE MANUELLEMENT ────────────────────────────── */

    public function lierDepense(Request $request, Fournisseur $fournisseur)
    {
        $request->validate(['depense_id' => 'required|exists:depenses,id']);

        Depense::findOrFail($request->depense_id)->update([
            'fournisseur_id' => $fournisseur->id,
            'updated_by'     => Auth::id(),
        ]);

        return back()->with('success', 'Dépense liée au fournisseur.');
    }
}

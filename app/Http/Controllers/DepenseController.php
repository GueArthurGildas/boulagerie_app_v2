<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\CategorieDepense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepenseController extends Controller
{
    /* ─── INDEX ──────────────────────────────────────────────── */
    public function index(Request $request)
    {
        $query = Depense::with(['categorie', 'createdBy'])
            ->orderByDesc('date_depense')
            ->orderByDesc('id');

        if ($request->filled('categorie'))     $query->where('categorie_depense_id', $request->categorie);
        if ($request->filled('mode'))          $query->where('mode_paiement', $request->mode);
        if ($request->filled('statut'))        $query->where('statut', $request->statut);
        if ($request->filled('date_debut'))    $query->whereDate('date_depense', '>=', $request->date_debut);
        if ($request->filled('date_fin'))      $query->whereDate('date_depense', '<=', $request->date_fin);
        if ($request->filled('q'))             $query->where('libelle', 'like', '%'.$request->q.'%');

        $depenses   = $query->paginate(20)->withQueryString();
        $categories = CategorieDepense::where('actif', true)->orderBy('nom')->get();

        // Totaux pour le résumé
        $totalMois = Depense::validees()
            ->whereMonth('date_depense', now()->month)
            ->whereYear('date_depense', now()->year)
            ->sum('montant');

        $totalFiltres = $query->toBase()->sum('montant');

        return view('depenses.index', compact('depenses', 'categories', 'totalMois', 'totalFiltres'));
    }

    /* ─── CREATE ─────────────────────────────────────────────── */
    public function create(Request $request)
    {
        $categories = CategorieDepense::where('actif', true)->orderBy('nom')->get();

        // Pré-remplir depuis un modèle récurrent (clone)
        $modele = null;
        if ($request->filled('clone')) {
            $modele = Depense::find($request->clone);
        }

        return view('depenses.create', compact('categories', 'modele'));
    }

    /* ─── STORE ──────────────────────────────────────────────── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'categorie_depense_id'  => 'required|exists:categorie_depenses,id',
            'libelle'               => 'required|string|max:200',
            'montant'               => 'required|integer|min:1',
            'mode_paiement'         => 'required|in:cash,orange_money,wave,mtn_momo,banque,autre',
            'reference_mobile'      => 'nullable|string|max:20',
            'date_depense'          => 'required|date',
            'beneficiaire'          => 'nullable|string|max:150',
            'notes'                 => 'nullable|string',
            'statut'                => 'nullable|in:brouillon,validee',
            'est_recurrente'        => 'nullable|boolean',
            'frequence_recurrence'  => 'nullable|in:hebdomadaire,mensuelle,trimestrielle,annuelle',
        ]);

        $data['statut']     = $request->statut ?? 'validee';
        $data['created_by'] = Auth::id();

        // Auto-valider si statut = validee
        if ($data['statut'] === 'validee') {
            $data['valide_par'] = Auth::id();
            $data['valide_le']  = now();
        }

        $depense = Depense::create($data);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    /* ─── SHOW ───────────────────────────────────────────────── */
    public function show(Depense $depense)
    {
        $depense->load(['categorie', 'createdBy', 'validePar']);
        return view('depenses.show', compact('depense'));
    }

    /* ─── EDIT ───────────────────────────────────────────────── */
    public function edit(Depense $depense)
    {
        if ($depense->statut === 'validee') {
            return back()->withErrors(['error' => 'Une dépense validée ne peut plus être modifiée.']);
        }

        $categories = CategorieDepense::where('actif', true)->orderBy('nom')->get();
        return view('depenses.edit', compact('depense', 'categories'));
    }

    /* ─── UPDATE ─────────────────────────────────────────────── */
    public function update(Request $request, Depense $depense)
    {
        if ($depense->statut === 'validee') {
            return back()->withErrors(['error' => 'Une dépense validée ne peut plus être modifiée.']);
        }

        $data = $request->validate([
            'categorie_depense_id' => 'required|exists:categorie_depenses,id',
            'libelle'              => 'required|string|max:200',
            'montant'              => 'required|integer|min:1',
            'mode_paiement'        => 'required|in:cash,orange_money,wave,mtn_momo,banque,autre',
            'reference_mobile'     => 'nullable|string|max:20',
            'date_depense'         => 'required|date',
            'beneficiaire'         => 'nullable|string|max:150',
            'notes'                => 'nullable|string',
            'statut'               => 'nullable|in:brouillon,validee',
            'est_recurrente'       => 'nullable|boolean',
            'frequence_recurrence' => 'nullable|in:hebdomadaire,mensuelle,trimestrielle,annuelle',
        ]);

        $data['updated_by'] = Auth::id();

        if (($data['statut'] ?? $depense->statut) === 'validee' && $depense->statut !== 'validee') {
            $data['valide_par'] = Auth::id();
            $data['valide_le']  = now();
        }

        $depense->update($data);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense mise à jour.');
    }

    /* ─── DESTROY ────────────────────────────────────────────── */
    public function destroy(Depense $depense)
    {
        if ($depense->statut === 'validee') {
            return back()->withErrors(['error' => 'Impossible de supprimer une dépense validée.']);
        }
        $depense->delete();
        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée.');
    }

    /* ─── VALIDER ────────────────────────────────────────────── */
    public function valider(Depense $depense)
    {
        if ($depense->statut !== 'brouillon') {
            return back()->withErrors(['error' => 'Seules les dépenses en brouillon peuvent être validées.']);
        }

        $depense->update([
            'statut'     => 'validee',
            'valide_par' => Auth::id(),
            'valide_le'  => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Dépense validée.');
    }

    /* ─── REJETER ────────────────────────────────────────────── */
    public function rejeter(Depense $depense)
    {
        if ($depense->statut !== 'brouillon') {
            return back()->withErrors(['error' => 'Seules les dépenses en brouillon peuvent être rejetées.']);
        }

        $depense->update([
            'statut'     => 'rejetee',
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Dépense rejetée.');
    }

    /* ─── CLONER (dépenses récurrentes) ──────────────────────── */
    public function cloner(Depense $depense)
    {
        return redirect()->route('depenses.create', ['clone' => $depense->id]);
    }

    /* ─── RAPPORT ────────────────────────────────────────────── */
    public function rapport(Request $request)
    {
        $mois  = $request->mois  ?? now()->month;
        $annee = $request->annee ?? now()->year;

        $debut = Carbon::create($annee, $mois, 1)->startOfMonth();
        $fin   = Carbon::create($annee, $mois, 1)->endOfMonth();

        // Total par catégorie ce mois
        $parCategorie = Depense::validees()
            ->whereBetween('date_depense', [$debut, $fin])
            ->select('categorie_depense_id', DB::raw('SUM(montant) as total'), DB::raw('COUNT(*) as nb'))
            ->with('categorie')
            ->groupBy('categorie_depense_id')
            ->orderByDesc('total')
            ->get();

        // Total par mode de paiement
        $parMode = Depense::validees()
            ->whereBetween('date_depense', [$debut, $fin])
            ->select('mode_paiement', DB::raw('SUM(montant) as total'), DB::raw('COUNT(*) as nb'))
            ->groupBy('mode_paiement')
            ->orderByDesc('total')
            ->get();

        // Mois précédent pour comparaison
        $debutPrecedent = Carbon::create($annee, $mois, 1)->subMonth()->startOfMonth();
        $finPrecedent   = Carbon::create($annee, $mois, 1)->subMonth()->endOfMonth();

        $totalMoisPrecedent = Depense::validees()
            ->whereBetween('date_depense', [$debutPrecedent, $finPrecedent])
            ->sum('montant');

        $totalMois = $parCategorie->sum('total');

        // Évolution journalière
        $parJour = Depense::validees()
            ->whereBetween('date_depense', [$debut, $fin])
            ->select(DB::raw('DATE(date_depense) as jour'), DB::raw('SUM(montant) as total'))
            ->groupBy('jour')
            ->orderBy('jour')
            ->get();

        // Liste des mois disponibles
        $moisDisponibles = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $moisDisponibles[] = ['mois' => $d->month, 'annee' => $d->year, 'label' => $d->locale('fr')->isoFormat('MMMM YYYY')];
        }

        return view('depenses.rapport', compact(
            'parCategorie', 'parMode', 'totalMois', 'totalMoisPrecedent',
            'parJour', 'mois', 'annee', 'moisDisponibles', 'debut'
        ));
    }

    /* ─── CATÉGORIES ─────────────────────────────────────────── */
    public function categories()
    {
        $categories = CategorieDepense::withCount('depenses')->orderBy('nom')->get();
        return view('depenses.categories', compact('categories'));
    }

    public function storeCategorie(Request $request)
    {
        $request->validate([
            'nom'     => 'required|string|max:100|unique:categorie_depenses,nom',
            'couleur' => 'nullable|string|max:7',
        ]);

        CategorieDepense::create([
            'nom'     => $request->nom,
            'couleur' => $request->couleur ?? '#888888',
        ]);

        return back()->with('success', 'Catégorie créée.');
    }

    public function destroyCategorie(CategorieDepense $categorieDepense)
    {
        if ($categorieDepense->depenses()->exists()) {
            return back()->withErrors(['error' => 'Impossible : des dépenses utilisent cette catégorie.']);
        }
        $categorieDepense->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }
}

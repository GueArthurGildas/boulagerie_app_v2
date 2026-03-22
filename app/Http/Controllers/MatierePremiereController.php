<?php

namespace App\Http\Controllers;

use App\Models\MatierePremiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatierePremiereController extends Controller
{
    public function index()
    {
        $matieres = MatierePremiere::orderBy('nom')->paginate(25);
        return view('matieres.index', compact('matieres'));
    }

    public function create()
    {
        return view('matieres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'                => 'required|string|max:150|unique:matiere_premieres,nom',
            'unite'              => 'required|string|max:20',
            'stock_actuel'       => 'nullable|numeric|min:0',
            'stock_minimum'      => 'nullable|numeric|min:0',
            'prix_moyen_pondere' => 'nullable|integer|min:0',
            'date_peremption'    => 'nullable|date',
        ]);

        MatierePremiere::create([
            'nom'                => $request->nom,
            'unite'              => $request->unite,
            'stock_actuel'       => $request->stock_actuel ?? 0,
            'stock_minimum'      => $request->stock_minimum ?? 0,
            'prix_moyen_pondere' => $request->prix_moyen_pondere ?? 0,
            'date_peremption'    => $request->date_peremption,
            'created_by'         => Auth::id(),
        ]);

        return redirect()->route('matieres-premieres.index')
                         ->with('success', 'Matière première ajoutée.');
    }

    public function edit(MatierePremiere $matierePremiere)
    {
        $matiere = $matierePremiere;
        return view('matieres.edit', compact('matiere'));
    }

    public function update(Request $request, MatierePremiere $matierePremiere)
    {
        $request->validate([
            'nom'             => 'required|string|max:150|unique:matiere_premieres,nom,' . $matierePremiere->id,
            'unite'           => 'required|string|max:20',
            'stock_minimum'   => 'nullable|numeric|min:0',
            'date_peremption' => 'nullable|date',
        ]);

        $matierePremiere->update([
            'nom'             => $request->nom,
            'unite'           => $request->unite,
            'stock_minimum'   => $request->stock_minimum ?? 0,
            'date_peremption' => $request->date_peremption,
            'updated_by'      => Auth::id(),
        ]);

        return redirect()->route('matieres-premieres.index')
                         ->with('success', 'Matière première mise à jour.');
    }

    public function destroy(MatierePremiere $matierePremiere)
    {
        if ($matierePremiere->recetteLignes()->exists()) {
            return back()->withErrors(['error' => 'Impossible : cette matière est utilisée dans des recettes.']);
        }
        $matierePremiere->delete();
        return redirect()->route('matieres-premieres.index')
                         ->with('success', 'Matière première supprimée.');
    }
}

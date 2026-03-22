<?php

namespace App\Http\Controllers;

use App\Models\Recette;
use App\Models\MatierePremiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecetteController extends Controller
{
    public function index()
    {
        $recettes = Recette::withCount(['productions', 'lignes'])->paginate(20);
        return view('recettes.index', compact('recettes'));
    }

    public function create()
    {
        $matieres = MatierePremiere::where('actif', true)->orderBy('nom')->get();
        return view('recettes.create', compact('matieres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'                          => 'required|string|max:150',
            'description'                  => 'nullable|string',
            'nb_pieces_attendues'          => 'required|integer|min:1',
            'lignes'                       => 'required|array|min:1',
            'lignes.*.matiere_premiere_id' => 'required|exists:matiere_premieres,id',
            'lignes.*.quantite'            => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($request) {
            $recette = Recette::create([
                'nom'                 => $request->nom,
                'description'         => $request->description,
                'nb_pieces_attendues' => $request->nb_pieces_attendues,
                'created_by'          => Auth::id(),
            ]);
            foreach ($request->lignes as $ligne) {
                $recette->lignes()->create($ligne);
            }
        });

        return redirect()->route('recettes.index')->with('success', 'Recette créée.');
    }

    public function show(Recette $recette)
    {
        $recette->load('lignes.matierePremiere');
        return view('recettes.show', compact('recette'));
    }

    public function edit(Recette $recette)
    {
        $recette->load('lignes');
        $matieres = MatierePremiere::where('actif', true)->orderBy('nom')->get();
        return view('recettes.edit', compact('recette', 'matieres'));
    }

    public function update(Request $request, Recette $recette)
    {
        $request->validate([
            'nom'                          => 'required|string|max:150',
            'nb_pieces_attendues'          => 'required|integer|min:1',
            'lignes'                       => 'required|array|min:1',
            'lignes.*.matiere_premiere_id' => 'required|exists:matiere_premieres,id',
            'lignes.*.quantite'            => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($request, $recette) {
            $recette->update([
                'nom'                 => $request->nom,
                'description'         => $request->description,
                'nb_pieces_attendues' => $request->nb_pieces_attendues,
                'actif'               => $request->actif ?? $recette->actif,
                'updated_by'          => Auth::id(),
            ]);
            $recette->lignes()->delete();
            foreach ($request->lignes as $ligne) {
                $recette->lignes()->create($ligne);
            }
        });

        return redirect()->route('recettes.index')->with('success', 'Recette mise à jour.');
    }

    public function destroy(Recette $recette)
    {
        if ($recette->productions()->exists()) {
            return back()->withErrors(['error' => 'Impossible de supprimer une recette utilisée en production.']);
        }
        $recette->delete();
        return redirect()->route('recettes.index')->with('success', 'Recette supprimée.');
    }
}

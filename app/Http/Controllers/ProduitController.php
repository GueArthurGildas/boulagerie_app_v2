<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::orderBy('nom')->paginate(25);
        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'        => 'required|string|max:150|unique:produits,nom',
            'categorie'  => 'nullable|string|max:50',
            'prix_vente' => 'required|integer|min:0',
        ]);

        Produit::create([
            'nom'        => $request->nom,
            'categorie'  => $request->categorie,
            'prix_vente' => $request->prix_vente,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('produits.index')->with('success', 'Produit créé.');
    }

    public function edit(Produit $produit)
    {
        return view('produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom'        => 'required|string|max:150|unique:produits,nom,' . $produit->id,
            'categorie'  => 'nullable|string|max:50',
            'prix_vente' => 'required|integer|min:0',
            'actif'      => 'nullable|boolean',
        ]);

        $produit->update([
            'nom'        => $request->nom,
            'categorie'  => $request->categorie,
            'prix_vente' => $request->prix_vente,
            'actif'      => $request->actif ?? $produit->actif,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Produit $produit)
    {
        if ($produit->productionLignes()->exists()) {
            return back()->withErrors(['error' => 'Impossible : ce produit est lié à des productions.']);
        }
        $produit->delete();
        return redirect()->route('produits.index')->with('success', 'Produit supprimé.');
    }
}
